<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");

$recuperateur = new Recuperateur($_POST);

$action = $recuperateur->get('action');
$id_e = $recuperateur->getInt('id_e',0);
$libelle = $recuperateur->get('libelle');


$actionPossible = $objectInstancier->ActionPossible;

if ( ! $actionPossible->isActionPossibleOnEntite($id_e,$authentification->getId(),$libelle,$action)) {
	$lastError->setLastError("L'action « $action »  n'est pas permise : " .$actionPossible->getLastBadRule() );
	header("Location: detail.php?id_e=$id_e&page=$page");
	exit;
}

if ($id_e == 0){
	//$result = $objectInstancier->ActionExecutorFactory->executeOnGlobalProperties($authentification->getId(),$action);
} else {
	$result = $objectInstancier->ActionExecutorFactory->executeOnEntiteProperties2($id_e,$authentification->getId(),$libelle,$action);
}

$message = $objectInstancier->ActionExecutorFactory->getLastMessage();

if (! $result ){
	$lastError->setLastError($message);	
} else {
	$lastMessage->setLastMessage($message);	
}

header("Location: edition.php?id_e=$id_e&libelle=$libelle");

