<?php
require_once("init-api.php");

$recuperateur = new Recuperateur($_REQUEST);
$type = $recuperateur->get('type');

if ( ! $roleUtilisateur->hasOneDroit($id_u,"$type:lecture")) {
	$JSONoutput->displayErrorAndExit("Acces interdit type=$type,id_u=$id_u");
}


$documentType = $documentTypeFactory->getDocumentTypeContent($type);

$field = array();
foreach($documentType['formulaire'] as $onglet){
	$field += $onglet;
}
$JSONoutput->display($field);

