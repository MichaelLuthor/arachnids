<?php
use Arachids\Util\Path;
class Arachnids {
    /** @var Arachnids */
    private static $app = null;
    
    /** @return Arachnids */
    public static function getApp() {
        if ( null === self::$app ) {
            self::$app = new Arachnids();
        }
        return self::$app;
    }
    
    /** @var array */
    private $config = null;
    
    /**
     * @param unknown $name
     * @param unknown $default
     * @return mixed
     */
    public function getConfiguration( $name, $default=null ) {
        if ( null === $this->config ) {
            $this->config = require BASE_DIR.DIRECTORY_SEPARATOR.'Configuration'.DIRECTORY_SEPARATOR.'Main.conf.php';
        }
        return isset($this->config[$name]) ? $this->config[$name] : $default;
    }
    
    /**
     * @param unknown $path
     * @return \Arachids\Util\Path
     */
    public function getPath( $path ) {
        $path = BASE_DIR.DIRECTORY_SEPARATOR.$path;
        return Path::setup($path);
    }
}