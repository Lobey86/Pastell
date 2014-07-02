#! /usr/bin/php
<?php
require_once( __DIR__ . "/../../web/init.php");
require_once( __DIR__ . "/../batch/BLBatch.class.php");

$blScript = new BLBatch();

// Mise en place de l'extension BL : Connecteur ganeshtdtactesbl
$blScript->trace('Mise en place extension BL Connecteur ganeshtdtactesbl : ');
$requeteExtension = "SELECT id_e FROM extension WHERE path = ?";
$ext_ganeshtdtactesbl = "/var/www/pastell/extensionbl/ganeshtdtactesbl/";
if (!$sqlQuery->queryOne($requeteExtension, $ext_ganeshtdtactesbl)) {
    $sqlQuery->queryOne("INSERT INTO extension (path) VALUES(?)", $ext_ganeshtdtactesbl);
    $blScript->traceln('OK');
} else {
    $blScript->traceln('DEJA FAIT');
}
