<?php

require_once(dirname(__FILE__)."/../init-authenticated.php");
require_once (PASTELL_PATH . "/lib/formulaire/Formulaire.class.php");
require_once( PASTELL_PATH . "/lib/formulaire/DonneesFormulaire.class.php");

$recuperateur = new Recuperateur($_REQUEST);
$id_d = $recuperateur->get('id_d');
$id_e = $recuperateur->get('id_e');
$field = $recuperateur->get('field');
$page = $recuperateur->get('page');

$document = $objectInstancier->Document;
$info = $document->getInfo($id_d);
$type = $info['type'];
$titre = $info['titre'];

if ( ! $roleUtilisateur->hasDroit($authentification->getId(),$type.":edition",$id_e)) {
	$lastError->setLastError("Vous n'avez pas le droit de faire cette action ($type:edition)");
	header("Location: edition.php?id_d=$id_d&id_e=$id_e");
	exit;
}

$actionPossible = $objectInstancier->ActionPossible;


if ( ! $actionPossible->isActionPossible($id_e,$authentification->getId(),$id_d,'modification') ) {
	$lastError->setLastError("L'action « modification »  n'est pas permise : " .$actionPossible->getLastBadRule() );
	header("Location: detail.php?id_d=$id_d&id_e=$id_e&page=$page");
	exit;
}

$documentType = $documentTypeFactory->getDocumentType($type);
$formulaire = $documentType->getFormulaire();
$formulaire->setTabNumber($page);

$theField = $formulaire->getField($field);


$action_name = $theField->getProperties('choice-action');
if ($action_name) {
	$result = $objectInstancier->ActionExecutorFactory->goChoice($id_e,$authentification->getId(),$id_d,$action_name,false,$field,$page);	
} else {
	$script = $theField->getProperties('script-controler');
	require_once(PASTELL_PATH . "/externaldata/$script");
}


