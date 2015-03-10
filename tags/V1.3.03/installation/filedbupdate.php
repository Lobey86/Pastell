<?php
require_once( dirname(__FILE__) . "/../web/init.php");
require_once( PASTELL_PATH . "/lib/dbupdate/DatabaseUpdate.class.php");

$databaseUpdate = new DatabaseUpdate(false,$sqlQuery);
$databaseUpdate->writeDefinition(DATABASE_FILE,dirname(__FILE__)."/pastell.sql");

