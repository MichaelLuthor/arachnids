<?php
namespace Arachids\Util;
use Arachids\Util\UIHandler\Console;
abstract class BasicSpider {
    /** @var array */
    private $config = null;
    
    /**
     * @param unknown $name
     * @param unknown $default
     * @return mixed
     */
    protected function getConfiguration( $name, $default=null ) {
        if ( null === $this->config ) {
            $this->config = require $this->getPath(sprintf('Configuration/%s.conf.php', $this->getSpiderName()));
        }
        return isset($this->config[$name]) ? $this->config[$name] : $default;
    }
    
    /***
     * @param unknown $path
     * @param unknown $content
     */
    protected function saveFileContent( $path, $content ) {
        $path = $this->getPath(sprintf('Data/%s/%s', $this->getSpiderName(), $path));
        if ( !$path->getParentPath()->isDirectory() ) {
            $path->getParentPath()->createAsDirectory();
        }
        File::create($content)->renameOnExists()->save($path);
    }
    
    /**
     * @return string
     */
    protected function getSpiderName() {
        $classInfo = new \ReflectionClass($this);
        return $classInfo->getShortName();
    }
    
    /** @return \Arachids\Util\Path */
    public function getPath( $path ) {
        $spiderInfo = new \ReflectionClass($this);
        $spiderPath = dirname($spiderInfo->getFileName());
        $path = $spiderPath.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $path);
        return Path::setup($path);
    }
    
    /** @var \Arachids\Util\Progress */
    private $progress = null;
    
    /** @return \Arachids\Util\Progress */
    public function getProgress() {
        if ( null === $this->progress ) {
            $this->progress = new Progress($this);
        }
        return $this->progress;
    }
    
    /** @var \Arachids\Util\UIHandler\Console */
    private $console = null;
    
    /** @return \Arachids\Util\UIHandler\Console */
    public function getConsoleHandler() {
        if ( null === $this->console ) {
            $this->console = Console::getHandler();
        }
        return $this->console;
    }
    
    abstract public function run();
}