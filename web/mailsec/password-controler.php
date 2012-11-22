<?php
require_once( dirname(__FILE__) . "/../init.php");
require_once( PASTELL_PATH . '/lib/formulaire/AfficheurFormulaire.class.php');

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
	$_SESSION["consult_ok_{$key}_{$ip}"] = 1;
	header("Location: index.php?key=$key");
	exit;
} else {
	
	$lastError->setLastError("Le mot de passe est incorecte");
	header("Location: password.php?key=$key");
	exit;
}
