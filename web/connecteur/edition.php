<?php
require_once( dirname(__FILE__) . "/../init-authenticated.php");

$recuperateur = new Recuperateur($_GET);
$id_e = $recuperateur->getInt('id_e');
$libelle = $recuperateur->get('libelle');
$connecteur_id = $recuperateur->get('connecteur_id');


$droit_ecriture = $roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$id_e);

if ( ! $droit_ecriture ){
	header("Location: index.php");
	exit;
}

$entite = new Entite($sqlQuery,$id_e);
if ($id_e && ! $entite->exists()){
	header("Location: index.php");
	exit;
}

$lastError->setLastError("Not implemented !");
header("Location: ".SITE_BASE."/entite/detail.php?id_e=$id_e&page=2");