<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");


//Récupération des données
$recuperateur = new Recuperateur($_POST);
$id_d = $recuperateur->get('id_d');
$page = $recuperateur->get('page');
$id_e = $recuperateur->get('id_e');
$field = $recuperateur->get('field');
$num = $recuperateur->getInt('num',0);
$action = $recuperateur->get('action');


$document = $objectInstancier->Document;
$info = $document->getInfo($id_d);

$type = $info['type'];

if ( ! $roleUtilisateur->hasDroit($authentification->getId(),$type.":edition",$id_e)) {
	header("Location: list.php");
	exit;
}

$donneesFormulaire = $donneesFormulaireFactory->get($id_d,$type);

$actionPossible = $objectInstancier->ActionPossible;

if (!$action){
	$action = 'modification';
}


if ( ! $actionPossible->isActionPossible($id_e,$authentification->getId(),$id_d,$action) ) {
	$lastError->setLastError("L'action « $action »  n'est pas permise : " .$actionPossible->getLastBadRule() );
	header("Location: detail.php?id_d=$id_d&id_e=$id_e&page=$page");
	exit;
}


$documentType = $documentTypeFactory->getFluxDocumentType($type);
$formulaire = $documentType->getFormulaire();
$formulaire->setTabNumber($page);


$donneesFormulaire = $donneesFormulaireFactory->get($id_d,$type);
$donneesFormulaire->removeFile($field,$num);


header("Location: " . SITE_BASE . "document/edition.php?id_d=$id_d&id_e=$id_e&page=$page");
