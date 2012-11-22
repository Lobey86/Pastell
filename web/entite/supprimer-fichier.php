<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/FileUploader.class.php");
require_once (PASTELL_PATH . "/lib/formulaire/Formulaire.class.php");
require_once( PASTELL_PATH . "/lib/formulaire/DonneesFormulaire.class.php");

//Récupération des données
$recuperateur = new Recuperateur($_POST);
$page = $recuperateur->get('page');
$id_e = $recuperateur->get('id_e');
$field = $recuperateur->get('field');
$num = $recuperateur->getInt('num',0);

if ( ! $roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$id_e)) {
	header("Location: list.php");
	exit;
}

$documentType = $documentTypeFactory->getDocumentType('collectivite-properties');
$formulaire = $documentType->getFormulaire();
$formulaire->setTabNumber($page);

$donneesFormulaire = $donneesFormulaireFactory->get($id_e,'collectivite-properties');
$donneesFormulaire->removeFile($field,$num);


header("Location: " . SITE_BASE . "entite/edition-properties.php?id_e=$id_e&page=$page");
