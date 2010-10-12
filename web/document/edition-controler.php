<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");

require_once( PASTELL_PATH . "/lib/FileUploader.class.php");

require_once (PASTELL_PATH . "/lib/formulaire/Formulaire.class.php");
require_once( PASTELL_PATH . "/lib/formulaire/DonneesFormulaire.class.php");
require_once (PASTELL_PATH . "/lib/action/ActionCreator.class.php");

require_once (PASTELL_PATH . "/lib/document/Document.class.php");
require_once (PASTELL_PATH . "/lib/action/DocumentActionEntite.class.php");

require_once (PASTELL_PATH . "/lib/base/ZenMail.class.php");
require_once (PASTELL_PATH . "/lib/notification/Notification.class.php");
require_once (PASTELL_PATH . "/lib/notification/NotificationMail.class.php");
require_once (PASTELL_PATH . "/lib/document/DocumentEntite.class.php");

//Récupération des données
$recuperateur = new Recuperateur($_POST);
$id_d = $recuperateur->get('id_d');
$type = $recuperateur->get('form_type');
$id_e = $recuperateur->get('id_e');
$objet = $recuperateur->get('objet');
$page = $recuperateur->get('page');


if ( ! $roleUtilisateur->hasDroit($authentification->getId(),$type.":edition",$id_e)) {
	header("Location: list.php");
	exit;
}

$documentType = $documentTypeFactory->getDocumentType($type);
$formulaire = $documentType->getFormulaire();
$formulaire->setTabNumber($page);


$document = new Document($sqlQuery);
$info = $document->getInfo($id_d);
if (! $info){
	$document->save($id_d,$type);
}


$documentEntite = new DocumentEntite($sqlQuery);
$documentEntite->addRole($id_d,$id_e,"editeur");


$actionCreator = new ActionCreator($sqlQuery,$journal,$id_d);
if (! $info){
	$actionCreator->addAction($id_e,$authentification->getId(),Action::CREATION,"Création du document");
} else {
	$actionCreator->addAction($id_e,$authentification->getId(),Action::MODIFICATION,"Modification du document");
}

$fileUploader = new FileUploader($_FILES);


$donneesFormulaire = new DonneesFormulaire( WORKSPACE_PATH  . "/$id_d.yml");
$donneesFormulaire->setFormulaire($formulaire);
	
$donneesFormulaire->save($recuperateur,$fileUploader);

foreach($fileUploader->getAll() as $filename => $orig_filename){
	$url = WORKSPACE_PATH . "/$id_d" . "_" . $filename;
	$fileUploader->save($filename,$url);
}

$titre_field = $formulaire->getTitreField();

$titre = $donneesFormulaire->get($titre_field);

$document->setTitre($id_d,$titre);



$type = $recuperateur->get('suivant');
if ($type){
	header("Location: edition.php?id_d=$id_d&id_e=$id_e&page=".($page+1));
	exit;
}
$type = $recuperateur->get('precedent');
if ($type){
	header("Location: edition.php?id_d=$id_d&id_e=$id_e&page=".($page - 1));
	exit;
}
header("Location: " . SITE_BASE . "document/detail.php?id_d=$id_d&id_e=$id_e&page=$page");
