<?php
require_once( __DIR__ . "/../init.php");


$recuperateur = new Recuperateur();

$id_e = $recuperateur->get('id_e');

if (!$id_e){
	$objectInstancier->LastError->setLastError("Une erreur est survenue");
	header("Location: ".SITE_BASE."/connexion/connexion.php");	
	exit;
}

$openID = $objectInstancier->ConnecteurFactory->getConnecteurByType($id_e,'openid-authentification','openid-authentication');
if (!$openID){
	$objectInstancier->LastError->setLastError("Une erreur est survenue - pas de connecteur");
	header("Location: ".SITE_BASE."/connexion/connexion.php");
	exit;
}

$openID->authenticate();
