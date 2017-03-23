<?php
namespace Arachids\Spider\QianTuWang;
use Arachids\Util\BasicSpider;
use Arachids\Lib\SimpleHtmlDom\Parser;
use Arachids\Util\Progress;
class WebUISpider extends BasicSpider {
    /** @var array */
    private $progressUI = array(
        'TopLinkCurrentIndex' => 1,
        'TopLinkCount' => 0,
        'IndexPageCurrentIndex' => 1,
        'IndexPageCount' => 0,
        'ImageIndex' => 1,
        'ImageTotalCount' => 0,
        'ImageURL' => '',
        'ImageStatus' => '',
        'ImageMessage' => '',
    );
    
    /**
     * {@inheritDoc}
     * @see \Arachids\Util\BasicSpirder::run()
     */
    public function run() {
        $topLinks = $this->getConfiguration('top_links', array());
        $this->setUIVal('TopLinkCount', count($topLinks));
        
        foreach ( $topLinks as $index => $topLink ) {
            if ( $this->getProgress()->hasProcessed($topLink) ) {
                continue;
            }
            $this->processTopLink($topLink);
            $this->setUIVal('TopLinkCurrentIndex', $index+1);
        }
    }
    
    /** @return void */
    private function processTopLink( $link ) {
        $this->getProgress()->addTarget($link, 'TOP_LINK');
        
        $totalPage = 0;
        $currentPage = 1;
        do {
            $pageLink = sprintf('%sid-%d.html', $link, $currentPage);
            if ( $this->getProgress()->hasProcessed($pageLink) ) {
                $currentPage ++;
                continue;
            }
            
            $this->getProgress()->addTarget($pageLink, 'INDEX_PAGE');
            $client = new \GuzzleHttp\Client();
            try {
                $res = $client->get($pageLink, array('verify'=>false));
            } catch ( \GuzzleHttp\Exception\ServerException $e ) {
                $this->getProgress()->error($pageLink, $e->getMessage());
            }
            
            $html = Parser::parseString($res->getBody());
            if ( 0 === $totalPage ) {
                $pager = $html->find('#showpage', 0)->text();
                preg_match_all('#共(.*?)页#', $pager, $matches);
                $totalPage = (int)$matches[1][0];
                $this->setUIVal('IndexPageCount', $totalPage);
            }
            
            $images = $html->find('.thumb-box > img');
            $this->setUIVal('ImageTotalCount', count($images));
            foreach ( $images as $imageIndex => $image ) {
                $imageUrl = $image->getAttribute('src1');
                if ( false === $imageUrl ) {
                    $imageUrl = $image->getAttribute('src');
                }
                
                if ( $this->getProgress()->hasProcessed($imageUrl) ) {
                    continue;
                }
                
                $status = 'INIT';
                $error = '';
                $this->getProgress()->addTarget($imageUrl, 'IMAGE');
                try {
                    $this->saveImageFile($link, $imageUrl);
                    $this->getProgress()->success($imageUrl);
                    $status = 'YES';
                } catch ( \GuzzleHttp\Exception\RequestException $e ) {
                    $this->getProgress()->error($imageUrl, $e->getMessage());
                    $status = 'NO';
                    $error = $e->getMessage();
                }
                $this->setUIVal(array(
                    'ImageIndex' => $imageIndex+1,
                    'ImageURL' => $imageUrl,
                    'ImageStatus' => $status,
                    'ImageMessage' => $error,
                ));
            }
            $this->getProgress()->success($pageLink);
            $this->setUIVal('IndexPageCurrentIndex', $currentPage);
            $currentPage ++;
        }while (0 !== $totalPage && $currentPage <= $totalPage );
        
        $this->getProgress()->doneTarget($link, Progress::STATUS_SUCCESS);
    }
    
    /** @return void */
    private function saveImageFile( $topLink, $imageUrl ) {
        $folderMap = $this->getConfiguration('folder_map', array());
        $folderName = '';
        if ( isset($folderMap[$topLink]) ) {
            $folderName = $folderMap[$topLink];
        }
        
        $imageUrl = substr($imageUrl, 0, strpos($imageUrl, '!'));
        $client = new \GuzzleHttp\Client();
        $res = $client->get($imageUrl,array('verify'=>false));
        
        $fileName = $folderName.'/'.basename($imageUrl);
        $this->saveFileContent($fileName, $res->getBody());
    }
    
    /** @return void */
    private function setUIVal( $name, $value=null ) {
        if ( is_array($name) ) {
            $this->progressUI = array_merge($this->progressUI, $name);
        } else {
            $this->progressUI[$name] = $value;
        }
        
        $progress = $this->progressUI;
        $line = sprintf('Top[%d/%d] Page[%d/%d] Image[%d/%d] (%s) %s %s',
            $progress['TopLinkCurrentIndex'],
            $progress['TopLinkCount'],
            $progress['IndexPageCurrentIndex'],
            $progress['IndexPageCount'],
            $progress['ImageIndex'],
            $progress['ImageTotalCount'],
            $progress['ImageStatus'],
            $progress['ImageURL'],
            $progress['ImageMessage']
        );
        $this->getConsoleHandler()->writeLine($line);
    }
}