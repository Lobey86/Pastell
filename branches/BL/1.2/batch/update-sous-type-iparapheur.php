#! /usr/bin/php
<?php
require_once( dirname(__FILE__) . "/../web/init.php");

$id_ce = $objectInstancier->ConnecteurEntiteSQL->getGlobal('iParapheur');
$objectInstancier->ActionExecutorFactory->executeOnConnecteur($id_ce,0,'update-all-iparapheur');

echo $objectInstancier->ActionExecutorFactory->getLastMessage();
