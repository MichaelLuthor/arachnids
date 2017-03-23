<?php
namespace Arachids\Lib\SimpleHtmlDom;
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'simple_html_dom.php';
class Parser {
    /**
     * @param unknown $str
     * @return \simple_html_dom
     */
    public static function parseString( $str ) {
        return str_get_html($str);
    }
}