<?php
namespace Arachids\Util;
class Path {
    /**
     * @param unknown $path
     * @return \Arachids\Util\Path
     */
    public static function setup( $path ) {
        return new Path($path);
    }
    
    /** @var string */
    private $path = null;
    
    /** @var string */
    private $sysCharsetPath = null;
    
    /** @var Path */
    private $parent = null;
    
    /** @var string */
    public $filename = null;
    
    /** @var string */
    public $extension = null;
    
    /** @param unknown $path */
    public function __construct( $path ) {
        $this->path = str_replace('/', DIRECTORY_SEPARATOR, $path);
        $this->sysCharsetPath = MyString::convertToSysCharset($this->path);
        $this->parent = substr($this->sysCharsetPath, 0, strrpos($this->sysCharsetPath, DIRECTORY_SEPARATOR));
        
        $filename = str_replace($this->parent.DIRECTORY_SEPARATOR, '', $this->sysCharsetPath);
        $filename = explode('.', $filename);
        if ( 1 == count($filename) ) {
            $this->filename = $filename[0];
        } else {
            $this->extension = array_pop($filename);
            $this->filename = implode('.', $filename);
        }
    }
    
    /** @return string */
    public function __toString() {
        return $this->toString();
    }
    
    /** @return string */
    public function toString() {
        $path = $this->parent.DIRECTORY_SEPARATOR.$this->filename;
        if ( null !== $this->extension ) {
            $path .= '.'.$this->extension;
        }
        return $path;
    }
    
    /**
     * @return \Arachids\Util\Path
     */
    public function getParentPath() {
        $path = substr($this->path, 0, strrpos($this->path, DIRECTORY_SEPARATOR));
        return Path::setup($path);
    }
    
    /** @return boolean */
    public function isDirectory() {
        return is_dir($this->toString());
    }
    
    /** @return boolean */
    public function isFile() {
        return file_exists($this->toString());
    }
    
    /** @return void */
    public function createAsDirectory () {
        mkdir($this->toString(), 0777, true);
    }
}