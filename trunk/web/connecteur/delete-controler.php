<?php
require_once( dirname(__FILE__) . "/../init-authenticated.php");
require_once( PASTELL_PATH . '/lib/formulaire/AfficheurFormulaire.class.php');

$recuperateur = new Recuperateur($_POST);
$id_e = $recuperateur->getInt('id_e');
$libelle = $recuperateur->get('libelle');

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
$info = $entite->getInfo();

$objectInstancier->ConnecteurFactory->delete($id_e,$libelle);

$lastMessage->setLastMessage("Le connecteur $libelle a été supprimé");
header("Location: ".SITE_BASE."/entite/detail.php?id_e=$id_e&page=2");