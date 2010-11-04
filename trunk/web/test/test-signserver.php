<?php 

require_once(dirname(__FILE__)."/../init-authenticated.php");
require_once( PASTELL_PATH ."/lib/timestamp/OpensslTSWrapper.class.php");
require_once( PASTELL_PATH ."/lib/base/CurlWrapper.class.php");
require_once( PASTELL_PATH ."/lib/timestamp/SignServer.class.php");

if  (! $roleUtilisateur->hasDroit($authentification->getId(),'test:lecture',0)){
	header("Location: ".SITE_BASE . "/index.php");
	exit;
}

$data_to_horodate = "toto";

$opensslTSWrapper = new OpensslTSWrapper(OPENSSL_PATH,$zLog);
$signServer = new SignServer(SIGN_SERVER_URL,$opensslTSWrapper);

$timestamp_reply = $signServer->getTimestampReply($data_to_horodate);

if (! $timestamp_reply){
	$lastError->setLastError("Erreur sur la signature : " . $signServer->getLastError());
	header("Location: index.php");
	exit;
}


/*$result = $opensslTSWrapper->verify($data_to_horodate,$timestamp_reply,SIGN_SERVER_CERTIFICATE);


if (! $result){
	$lastError->setLastError("Erreur sur la vérification : " . $opensslTSWrapper->getLastError());
	header("Location: index.php");
	exit;
}*/

$lastMessage->setLastMessage("Ok timestamp = " . $signServer->getLastTimestamp());

header("Location: index.php");