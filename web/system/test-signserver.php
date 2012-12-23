<?php 

require_once(dirname(__FILE__)."/../init-authenticated.php");

if  (! $roleUtilisateur->hasDroit($authentification->getId(),'test:lecture',0)){
	header("Location: ".SITE_BASE . "/index.php");
	exit;
}

$data_to_horodate = "toto";

$timestamp_reply = $signServer->getTimestampReply($data_to_horodate);

if (! $timestamp_reply){
	$lastError->setLastError("Erreur sur la signature : " . $signServer->getLastError());
	header("Location: index.php");
	exit;
}

$lastMessage->setLastMessage("Ok timestamp = " . $signServer->getLastTimestamp());

header("Location: index.php?page_number=1");