<?php
# set up base dir
define('BASE_DIR', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);

/**
 * @param unknown $path
 */
function import( $path ) {
    $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
    require_once BASE_DIR.$path;
}

# import commom libs
import('Lib/Guzzle/autoloader.php');
import('Lib/ActiveRecord/ActiveRecord.php');
# register autoloader
spl_autoload_register(function( $class ) {
    $classFile = explode('\\', $class);
    array_shift($classFile);
    $classFile = BASE_DIR.implode(DIRECTORY_SEPARATOR, $classFile).'.php';
    if ( file_exists($classFile) ) {
        require $classFile;
    }
});