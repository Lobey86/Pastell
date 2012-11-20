<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");
require_once (PASTELL_PATH . "/lib/action/ActionPossible.class.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");

$recuperateur = new Recuperateur($_POST);

$action = $recuperateur->get('action');
$id_e = 0;
$page = $recuperateur->getInt('page',0);


$documentType = $documentTypeFactory->getDocumentType('entite0-properties');
$theAction = $documentType->getAction();
$formulaire = $documentType->getFormulaire();

$actionName = $theAction->getActionName($action);


$entite = new Entite($sqlQuery,0);

$actionPossible = new ActionPossible($sqlQuery,$id_e,$authentification->getId(),$theAction);
$actionPossible->setRoleUtilisateur($roleUtilisateur);
//$actionPossible->setDonnesFormulaire($donneesFormulaire);
$actionPossible->setEntite($entite);

if ( ! $actionPossible->isActionPossible($id_e,$action)) {
	$lastError->setLastError("L'action « $action »  n'est pas permise : " .$actionPossible->getLastBadRule() );
	header("Location: detail.php?id_e=$id_e&page=$page");
	exit;
}



$action_class_name = $theAction->getActionClass($action);

$action_class_file = dirname(__FILE__)."/../../action/$action_class_name.class.php";

if (! file_exists($action_class_file )){
	$lastError->setLastError("L'action « $action » est inconnue, veuillez contacter votre administrateur Pastell");	
	header("Location: detail0.php?id_e=$id_e&page=$page");
	exit;
}

require_once($action_class_file);

$actionClass = new $action_class_name($zLog,$sqlQuery,$id_e,$id_e,$authentification->getId(),'collectivite-properties');

$result = $actionClass->go();

if ($result){
	$lastMessage->setLastMessage($actionClass->getLastMessage());
} else {
	$lastError->setLastError($actionClass->getLastMessage());
}

header("Location: detail0.php?id_e=$id_e&page=$page");
