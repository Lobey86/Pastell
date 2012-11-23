<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");
require_once (PASTELL_PATH . "/lib/action/ActionPossible.class.php");

$recuperateur = new Recuperateur($_POST);

$action = $recuperateur->get('action');
$id_e = $recuperateur->getInt('id_e',0);
$page = $recuperateur->getInt('page',0);

$document_type = $id_e==0?'entite0-properties':'collectivite-properties';

$documentType = $documentTypeFactory->getDocumentType($document_type);
$theAction = $documentType->getAction();
$formulaire = $documentType->getFormulaire();

$actionName = $theAction->getActionName($action);
$donneesFormulaire = $donneesFormulaireFactory->get($id_e,$document_type);

$entite = new Entite($sqlQuery,$id_e);

$actionPossible = new ActionPossible($sqlQuery,$id_e,$authentification->getId(),$theAction);
$actionPossible->setRoleUtilisateur($roleUtilisateur);
$actionPossible->setDonnesFormulaire($donneesFormulaire);
$actionPossible->setEntite($entite);

if ( ! $actionPossible->isActionPossible($id_e,$action)) {
	$lastError->setLastError("L'action « $action »  n'est pas permise : " .$actionPossible->getLastBadRule() );
	header("Location: detail.php?id_e=$id_e&page=$page");
	exit;
}

if ($id_e == 0){
	$result = $objectInstancier->ActionExecutorFactory->executeOnGlobalProperties($authentification->getId(),$action);
} else {
	$result = $objectInstancier->ActionExecutorFactory->executeOnEntiteProperties($id_e,$authentification->getId(),$action);
}

$message = $objectInstancier->ActionExecutorFactory->getLastMessage();

if (! $result ){
	$lastError->setLastError($message);	
} else {
	$lastMessage->setLastMessage($message);	
}

if ($id_e){
	header("Location: detail.php?id_e=$id_e&page=$page");
} else {
	header("Location: detail0.php?id_e=$id_e&page=$page");
}
