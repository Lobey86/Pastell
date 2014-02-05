<?php
require_once( dirname(__FILE__) . "/../web/init.php");
require_once( PASTELL_PATH . "/lib/dbupdate/DatabaseUpdate.class.php");

$databaseUpdate = new DatabaseUpdate(file_get_contents(DATABASE_FILE),$sqlQuery);
$sqlCommand = $databaseUpdate->getDiff();

echo implode("\n",$sqlCommand);
exit($sqlCommand?1:0);
