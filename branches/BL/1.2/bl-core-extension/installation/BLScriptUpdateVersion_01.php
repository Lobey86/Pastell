#! /usr/bin/php
<?php 
require_once( __DIR__ . "/../../web/init.php");
require_once( __DIR__ . "/../batch/BLBatch.class.php");

$blscript = new BLBatch();

//Suppresion du droit system:modification sur le role adminEntite.
$blscript->trace('Suppression du droit \'system:edition\' du rôle \'adminEntite\' : ');
$param =array("adminEntite", "system:edition");
$droitExist = $sqlQuery->queryOne("SELECT * FROM role_droit WHERE role=? and droit=?", $param);
if ($droitExist) {
    $sqlQuery->queryOne("DELETE FROM role_droit WHERE role=? and droit=?", $param);
    $blscript->traceln('OK');
} else {
    $blscript->traceln('DEJA FAIT');
}
