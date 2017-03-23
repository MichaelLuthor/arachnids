<?php
include 'arachnids.php';
include 'Util/bootstrap.php';

if ( !isset($argv[1]) ) {
    die("No spirder.");
}

$spirderName = str_replace('/', '\\', $argv[1]);
$spirderHandler = sprintf("\\Arachids\\Spider\\%s", $spirderName);
if ( !class_exists($spirderHandler) ) {
    die("No spirder.");
}

$spirder = new $spirderHandler();
$spirder->run();