#! /usr/bin/php
<?php 
require_once( __DIR__ . "/../../web/init.php");
require_once( __DIR__ . "/../batch/BLBatch.class.php");

$blScript = new BLBatch();

// Suppresion du droit system:modification sur le role adminEntite.
$blScript->trace('Suppression du droit \'system:edition\' du rôle \'adminEntite\' : ');
$param =array("adminEntite", "system:edition");
$droitExist = $sqlQuery->queryOne("SELECT * FROM role_droit WHERE role=? and droit=?", $param);
if ($droitExist) {
    $sqlQuery->queryOne("DELETE FROM role_droit WHERE role=? and droit=?", $param);
    $blScript->traceln('OK');
} else {
    $blScript->traceln('DEJA FAIT');
}

// Mise en place de l'extension BL : Connecteur fasttdtheliosbl
$blScript->trace('Mise en place extension BL Connecteur fasttdtheliosbl : ');
$prov_extension = false;
$requeteExtension = "SELECT id_e FROM extension WHERE path = ?";
$ext_fluxbl = "/var/www/pastell/extensionbl/fasttdtheliosbl/";
if (!$sqlQuery->queryOne($requeteExtension, $ext_fluxbl)) {
    $sqlQuery->queryOne("INSERT INTO extension (path) VALUES(?)", $ext_fluxbl);
    $blScript->traceln('OK');
} else {
    $blScript->traceln('DEJA FAIT');
}
