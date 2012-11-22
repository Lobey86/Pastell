#! /usr/bin/php
<?php
require_once( dirname(__FILE__) . "/../web/init.php");

$journal = new Journal($signServer, $sqlQuery,0);
$journal->horodateAll();