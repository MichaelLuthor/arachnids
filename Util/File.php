<?php
namespace Arachids\Util;
class File {
    /**
     * @param unknown $content
     * @return \Arachids\Util\File
     */
    public static function create( $content ) {
        return new File($content);
    }
    
    /** @var mixed */
    private $content = null;
    
    /**
     * @param unknown $content
     */
    public function __construct( $content ) {
        return $this->content = $content;
    }
    
    /** @var boolean */
    private $renameOnFileExists = false;
    
    /** @return \Arachids\Util\File */
    public function renameOnExists() {
        $this->renameOnFileExists = true;
        return $this;
    }
    
    /**
     * @param unknown $path
     */
    public function save( $path ) {
        if ( !($path instanceof Path) ) {
            $path = Path::setup($path);
        }
        if ( $this->renameOnFileExists && $path->isFile()) {
            $this->generateFileName($path);
        }
        file_put_contents($path, $this->content);
    }
    
    /**
     * @param Path $path
     * @return string
     */
    private function generateFileName( Path $path ) {
        $index = 1;
        $basename = $path->filename;
        do {
            $path->filename = $basename."_".$index;
            $index ++;
        } while( $path->isFile() );
    }
}