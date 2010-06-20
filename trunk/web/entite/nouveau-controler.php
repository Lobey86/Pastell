<?php
require_once(dirname(__FILE__)."/../init-admin.php");

require_once( ZEN_PATH . "/lib/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/Redirection.class.php");

require_once( PASTELL_PATH . "/lib/Siren.class.php");


$recuperateur = new Recuperateur($_POST);

$nom = $recuperateur->get('nom');
$siren = $recuperateur->get('siren');
$type = $recuperateur->get('type');
$mere_siren =  $recuperateur->get('mere_siren');

$redirection = new Redirection("nouveau.php?type=$type");


$entite = new Entite($sqlQuery,$siren);

if (!$siren){
	$lastError->setLastError("Le siren est obligatoire");
	$redirection->redirect();
}

$sirenVerifier = new Siren();

if (! $sirenVerifier->isValid($siren)){
	$lastError->setLastError("Votre siren ne semble pas valide");
	$redirection->redirect();
}

if (!$nom){
	$lastError->setLastError("Le nom est obligatoire");
	$redirection->redirect();
}

if ($entite->exists()){
	$entite->update($nom,$type,$mere_siren);
} else {
	$entite->save($nom,$type,$mere_siren);
	$entite->setEtat(Entite::ETAT_VALIDE);
}
header("Location: collectivite.php");