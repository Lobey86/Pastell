<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");

require_once( PASTELL_PATH . "/lib/FileUploader.class.php");

require_once (PASTELL_PATH . "/lib/formulaire/Formulaire.class.php");
require_once( PASTELL_PATH . "/lib/formulaire/DonneesFormulaire.class.php");
require_once (PASTELL_PATH . "/lib/action/ActionCreator.class.php");

//Récupération des données
$recuperateur = new Recuperateur($_POST);
$id_e = $recuperateur->get('id_e');
$page = $recuperateur->get('page');


if ( ! $roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$id_e)) {
	header("Location: list.php");
	exit;
}

$documentType = $documentTypeFactory->getDocumentType('collectivite-properties');
$formulaire = $documentType->getFormulaire();
$formulaire->setTabNumber($page);


$fileUploader = new FileUploader($_FILES);


$donneesFormulaire = new DonneesFormulaire( WORKSPACE_PATH  . "/$id_e.yml");
$donneesFormulaire->setFormulaire($formulaire);
	
$donneesFormulaire->save($recuperateur,$fileUploader);


$type = $recuperateur->get('suivant');
if ($type){
	header("Location: edition-properties.php?id_e=$id_e&page=".($page+1));
	exit;
}
$type = $recuperateur->get('precedent');
if ($type){
	header("Location: edition-properties.php?id_e=$id_e&page=".($page - 1));
	exit;
}
header("Location: " . SITE_BASE . "entite/detail.php?id_e=$id_e&page=$page");
