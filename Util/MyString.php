<?php
namespace Arachids\Util;
class MyString {
    public static function convertToSysCharset( $string ) {
        $charset = \Arachnids::getApp()->getConfiguration('charset', 'UTF-8');
        if ( 'UTF-8' === strtoupper($charset) ) {
            return $string;
        }
        return iconv('UTF-8', $charset, $string);
    }
}