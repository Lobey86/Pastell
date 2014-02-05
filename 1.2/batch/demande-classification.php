#! /usr/bin/php
<?php
require_once( dirname(__FILE__) . "/../web/init.php");

$id_ce = $objectInstancier->ConnecteurEntiteSQL->getOne('s2low');
$result = $objectInstancier->ActionExecutorFactory->executeOnConnecteur($id_ce,0,'demande-classification');
