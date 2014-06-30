#! /usr/bin/php
<?php 
require_once( __DIR__ . "/../../web/init.php");
require_once( __DIR__ . "/../batch/BLBatch.class.php");

$blscript = new BLBatch();
$table_extension_exist = $sqlQuery->query("SHOW TABLES LIKE 'extension';");
if (empty($table_extension_exist)) {
    $sqlQuery->query("CREATE TABLE extension (id_e int(11) NOT NULL AUTO_INCREMENT, nom varchar(128) NOT NULL, path text NOT NULL, PRIMARY KEY (id_e))  ENGINE=MyISAM;");    
}
// Mise en place des extensions BL
$blscript->trace('Mise en place extension BL : ');
$prov_extension = false;
$requeteExtension = "SELECT id_e FROM extension WHERE path = ?";
$ext_fluxbl = "/var/www/pastell/extensionbl/fluxbl/";
if (!$sqlQuery->queryOne($requeteExtension, $ext_fluxbl)) {
    $sqlQuery->queryOne("INSERT INTO extension (path) VALUES(?)", $ext_fluxbl);
    $prov_extension=true;
}

$ext_iparapheurbl = "/var/www/pastell/extensionbl/iparapheurbl/";
if (!$sqlQuery->queryOne($requeteExtension, $ext_iparapheurbl)) {
    $sqlQuery->queryOne("INSERT INTO extension (path) VALUES(?)", $ext_iparapheurbl);
    $prov_extension=true;
}

$ext_s2lowbl = "/var/www/pastell/extensionbl/s2lowbl/";
if (!$sqlQuery->queryOne($requeteExtension, $ext_s2lowbl)) {
    $sqlQuery->queryOne("INSERT INTO extension (path) VALUES(?)", $ext_s2lowbl);
    $prov_extension=true;
}

$ext_srcibl = "/var/www/pastell/extensionbl/srcibl/";
if (!$sqlQuery->queryOne($requeteExtension, $ext_srcibl)) {
    $sqlQuery->queryOne("INSERT INTO extension (path) VALUES(?)", $ext_srcibl);
    $prov_extension=true;
}

$ext_stelabl = "/var/www/pastell/extensionbl/stelabl/";
if (!$sqlQuery->queryOne($requeteExtension, $ext_stelabl)) {
    $sqlQuery->queryOne("INSERT INTO extension (path) VALUES(?)", $ext_stelabl);
    $prov_extension=true;
}

$ext_globalbl = "/var/www/pastell/extensionbl/globalbl/";
if (!$sqlQuery->queryOne($requeteExtension, $ext_globalbl)) {
    $sqlQuery->queryOne("INSERT INTO extension (path) VALUES(?)", $ext_globalbl);
    $prov_extension=true;
}

$ext_xflucobl = "/var/www/pastell/extensionbl/xflucobl/";
if (!$sqlQuery->queryOne($requeteExtension, $ext_xflucobl)) {
    $sqlQuery->queryOne("INSERT INTO extension (path) VALUES(?)", $ext_xflucobl);
    $prov_extension=true;
}
if ($prov_extension) {
    $blscript->traceln('OK');    
} else {
    $blscript->traceln('DEJA FAIT');    
}

