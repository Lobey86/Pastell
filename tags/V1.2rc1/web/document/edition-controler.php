<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");

$recuperateur = new Recuperateur($_POST);
$id_d = $recuperateur->get('id_d');
$type = $recuperateur->get('form_type');
$id_e = $recuperateur->get('id_e');
$page = $recuperateur->get('page');
$action = $recuperateur->get('action');


if ( ! $roleUtilisateur->hasDroit($authentification->getId(),$type.":edition",$id_e)) {
	header("Location: list.php");
	exit;
}

$documentType = $documentTypeFactory->getFluxDocumentType($type);
$formulaire = $documentType->getFormulaire();

$donneesFormulaire = $donneesFormulaireFactory->get($id_d,$type);

$formulaire->addDonnesFormulaire($donneesFormulaire);
$formulaire->setTabNumber($page);


$document = $objectInstancier->Document;
$info = $document->getInfo($id_d);
if (! $info){
	$document->save($id_d,$type);
	$action = 'creation';
}

if (!$action){
	$action = 'modification';
}


$entite = new Entite($sqlQuery,$id_e);

$actionPossible = $objectInstancier->ActionPossible;



if ( ! $actionPossible->isActionPossible($id_e,$authentification->getId(),$id_d,$action)) {
	$lastError->setLastError("L'action « $action »  n'est pas permise : " .$actionPossible->getLastBadRule() );
	header("Location: detail.php?id_d=$id_d&id_e=$id_e&page=$page");
	exit;
}

$last_action = $objectInstancier->DocumentActionEntite->getLastActionNotModif($id_e, $id_d);

$editable_content = $documentType->getAction()->getEditableContent($last_action);


if ( (! in_array($last_action,array("creation","modification"))) || $editable_content){
	if ($editable_content){
		$donneesFormulaire->setEditableContent($editable_content);
	}
}

$fileUploader = new FileUploader();

$donneesFormulaire->saveTab($recuperateur,$fileUploader,$page);

if ($action=='creation' || $action=='modification'){
	$documentEntite = new DocumentEntite($sqlQuery);
	$documentEntite->addRole($id_d,$id_e,"editeur");
}


$actionCreator = new ActionCreator($sqlQuery,$journal,$id_d);
if (! $info){
	$actionCreator->addAction($id_e,$authentification->getId(),Action::CREATION,"Création du document");
} else if ($donneesFormulaire->isModified() ) {
	
	$actionCreator->updateModification($id_e, $authentification->getId(),$action);
} 


$titre_field = $formulaire->getTitreField();
$titre = $donneesFormulaire->get($titre_field);

$document->setTitre($id_d,$titre);

foreach($donneesFormulaire->getOnChangeAction() as $action_on_change) {	
	$result = $objectInstancier->ActionExecutorFactory->executeOnDocument($id_e,$authentification->getId(),$id_d,$action_on_change);
}

if ( $recuperateur->get('ajouter') ){
	header("Location: edition.php?id_d=$id_d&id_e=$id_e&page=$page&action=$action");
	exit;
}
if ( $recuperateur->get('suivant') ){
	header("Location: edition.php?id_d=$id_d&id_e=$id_e&action=$action&page=".($page+1));
	exit;
}

if ($recuperateur->get('precedent')){
	header("Location: edition.php?id_d=$id_d&id_e=$id_e&action=$action&page=".($page - 1));
	exit;
}

header("Location: " . SITE_BASE . "document/detail.php?id_d=$id_d&id_e=$id_e&page=$page&action=$action");
