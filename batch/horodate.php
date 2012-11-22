#! /usr/bin/php
<?php
require_once( dirname(__FILE__) . "/../web/init.php");

require_once (PASTELL_PATH . "/lib/journal/Journal.class.php");

$journal = new Journal($signServer, $sqlQuery,0);
$journal->horodateAll();