<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");

$recuperateur = new Recuperateur($_POST);
$id_d = $recuperateur->get('id_d');
$action = $recuperateur->get('action');
$id_e = $recuperateur->get('id_e');
$page = $recuperateur->getInt('page',0);
$go = $recuperateur->getInt('go',0);

$document = $objectInstancier->Document;
$infoDocument = $document->getInfo($id_d);
$type = $infoDocument['type'];

$documentType = $documentTypeFactory->getFluxDocumentType($type);
$theAction = $documentType->getAction();

$actionPossible = $objectInstancier->ActionPossible;

if ( ! $actionPossible->isActionPossible($id_e,$authentification->getId(),$id_d,$action)) {
	$lastError->setLastError("L'action « $action »  n'est pas permise : " .$actionPossible->getLastBadRule() );
	header("Location: detail.php?id_d=$id_d&id_e=$id_e&page=$page");
	exit;
}

if ($action == Action::MODIFICATION){
	header("Location: edition.php?id_d=$id_d&id_e=$id_e&page=$page");
	exit;
}


$id_destinataire = $recuperateur->get('destinataire')?:array();

$action_destinataire =  $theAction->getActionDestinataire($action);
if ($action_destinataire) {
	
	if (! $id_destinataire){
		header("Location: " . SITE_BASE . "/entite/choix-entite.php?id_d=$id_d&id_e=$id_e&action=$action&type=$action_destinataire");
		exit;
	}
}

if ($theAction->getWarning($action) && ! $go){
	header("Location: warning.php?id_d=$id_d&id_e=$id_e&action=$action&page=$page");
	exit;
}

$result = $objectInstancier->ActionExecutorFactory->executeOnDocument($id_e,$authentification->getId(),$id_d,$action,$id_destinataire);
$message = $objectInstancier->ActionExecutorFactory->getLastMessage();

if (! $result ){
	$lastError->setLastError($message);	
} else {
	$lastMessage->setLastMessage($message);	
}

header("Location: detail.php?id_d=$id_d&id_e=$id_e&page=$page");




