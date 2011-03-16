<?php
require_once( dirname(__FILE__) . "/../init.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/document/DocumentEmail.class.php");
require_once( PASTELL_PATH . "/lib/journal/Journal.class.php");
require_once( PASTELL_PATH ."/lib/timestamp/OpensslTSWrapper.class.php");
require_once( PASTELL_PATH ."/lib/base/CurlWrapper.class.php");
require_once( PASTELL_PATH ."/lib/timestamp/SignServer.class.php");
require_once( PASTELL_PATH ."/lib/action/ActionCreator.class.php");
require_once( PASTELL_PATH . '/lib/formulaire/AfficheurFormulaire.class.php');
require_once( PASTELL_PATH . "/lib/document/DocumentEntite.class.php");

$recuperateur = new Recuperateur($_POST);
$key = $recuperateur->get('key');
$password = $recuperateur->get('password');

$documentEmail = new DocumentEmail($sqlQuery);
$info  = $documentEmail->getInfoFromKey($key);

if (! $info ){
	header("Location: invalid.php");
	exit;
}

$donneesFormulaire = $donneesFormulaireFactory->get($info['id_d'],'mailsec-destinataire');

if ($donneesFormulaire->get('password') == $password){
	$ip = $_SERVER['REMOTE_ADDR'];
	apc_add("consult_ok_{$key}_{$ip}","1",60*5);
	header("Location: index.php?key=$key");
	exit;
} else {
	
	$lastError->setLastError("Le mot de passe est incorecte");
	header("Location: password.php?key=$key");
	exit;
}
