<?php
namespace Arachids\Util;
use Arachids\Util\Model\ProgressModel;
use function GuzzleHttp\json_encode;
class Progress {
    const STATUS_INIT = 'INIT';
    const STATUS_SUCCESS = 'SUCCESS';
    const STATUS_FAILED = 'FAILED';
    
    /** @var BasicSpider */
    private $spider = null;
    
    /** @var array */
    private $progressItems = array();
    
    /**
     * @param BasicSpider $spider
     */
    public function __construct( BasicSpider $spider ) {
        $dbPath = $spider->getPath('Data/data.db');
        $initRequired = !$dbPath->isFile();
        
        \ActiveRecord\Config::initialize(function ($cfg) use ($dbPath) {
            $cfg->set_model_directory(\Arachnids::getApp()->getPath('Util/Model'));
            $dbPath = urlencode(str_replace(DIRECTORY_SEPARATOR, '/', $dbPath));
            $system = (false === strpos(PHP_OS, 'WIN')) ? 'unix' : 'windows';
            $cfg->set_connections(array('spider_db' => "sqlite://{$system}({$dbPath})"));
        });
        
        if ( $initRequired ) {
            $this->setupSpiderDB();
        }
        \ActiveRecord\Table::clear_cache();
    }
    
    /** @return void */
    private function setupSpiderDB() {
        ProgressModel::createTable();
    }
    
    /**
     * @param unknown $target
     * @param unknown $type
     */
    public function addTarget($target, $type) {
        if ( isset($this->progressItems[$target]) ) {
            return;
        }
        
        $progress = new ProgressModel();
        $progress->set_attributes(array(
            'target' => $target,
            'type' => $type,
            'status' => self::STATUS_INIT,
            'start_at' => date('Y-m-d H:i:s'),
        ));
        $progress->save();
        $this->progressItems[$target] = $progress;
    }
    
    /**
     * @param unknown $target
     * @param unknown $status
     * @param array $data
     */
    public function success($target, $data=array()) {
        /** @var $progress ProgressModel */
        $progress = $this->progressItems[$target];
        $progress->set_attributes(array(
            'status' => self::STATUS_SUCCESS,
            'data' => json_encode($data),
            'message' => '',
            'end_at' => date('Y-m-d H:i:s'),
        ));
        $progress->save();
        unset($this->progressItems[$target]);
    }
    
    /**
     * @param unknown $target
     * @param unknown $status
     * @param array $data
     */
    public function error($target, $message='') {
        /** @var $progress ProgressModel */
        $progress = $this->progressItems[$target];
        $progress->set_attributes(array(
            'status' => self::STATUS_FAILED,
            'data' => json_encode(array()),
            'message' => $message,
            'end_at' => date('Y-m-d H:i:s'),
        ));
        $progress->save();
        unset($this->progressItems[$target]);
    }
    
    /**
     * @param unknown $target
     * @return boolean
     */
    public function hasProcessed( $target ) {
       $progress = ProgressModel::find(array('target'=>$target));
       if ( null === $progress ) {
           return false;
       }
       
       if ( $progress->status === self::STATUS_SUCCESS ) {
           return true;
       }
       $this->progressItems[$target] = $progress;
       return false;
    }
}