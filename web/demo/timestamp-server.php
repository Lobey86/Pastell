<?php


require_once(dirname(__FILE__)."/../init.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/timestamp/TimestampReplyCreator.class.php");

$recuperateur = new Recuperateur($_GET);
$data = $recuperateur->get('data');

$data = base64_decode($data);

$signerKey = PASTELL_PATH . "data-exemple/timestamp-key.pem";
$signerKeyPassword = "timestamp";
$configFile = PASTELL_PATH . "data-exemple/openssl-tsa.cnf";

$timestampReplyCreator = new TimestampReplyCreator(OPENSSL_PATH,SIGN_SERVER_CERTIFICATE,$signerKey,$signerKeyPassword,$configFile);

$timestamp_reply = $timestampReplyCreator->createTimestampReply($data);


header("Content-type: application/timestamp-reply");
header("Content-length: " . strlen($timestamp_reply));

echo $timestamp_reply;
