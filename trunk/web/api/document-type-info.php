<?php
require_once("init-api.php");

$recuperateur = new Recuperateur($_REQUEST);
$type = $recuperateur->get('type');

if ( ! $roleUtilisateur->hasOneDroit($id_u,"$type:lecture")) {
	$JSONoutput->displayErrorAndExit("Acces interdit type=$type,id_u=$id_u");
}


$documentType = $documentTypeFactory->getDocumentType($type);
$formulaire = $documentType->getFormulaire();

foreach($formulaire->getAllFields() as $key => $fields){	
	$result[$key] = $fields->getAllProperties(); 	
}

$JSONoutput->display($result);

