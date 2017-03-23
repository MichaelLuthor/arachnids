<?php
namespace Arachids\Util\UIHandler;
class Console {
    public static function getHandler() {
        return new Console();
    }
    
    public function writeLine( $string ) {
        printf("%s\n", $string);
    }
}