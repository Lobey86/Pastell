<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");

require_once( PASTELL_PATH . "/lib/FileUploader.class.php");

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

$donneesFormulaire = $donneesFormulaireFactory->get($id_d,$type);

$formulaire->addDonnesFormulaire($donneesFormulaire);
$formulaire->setTabNumber($page);


$document = new Document($sqlQuery);
$info = $document->getInfo($id_d);
if (! $info){
	$document->save($id_d,$type);
}


$fileUploader = new FileUploader($_FILES);

$donneesFormulaire->saveTab($recuperateur,$fileUploader,$page);

$documentEntite = new DocumentEntite($sqlQuery);
$documentEntite->addRole($id_d,$id_e,"editeur");


$actionCreator = new ActionCreator($sqlQuery,$journal,$id_d);
if (! $info){
	$actionCreator->addAction($id_e,$authentification->getId(),Action::CREATION,"Création du document");
} else if ($donneesFormulaire->isModified()) {
	$actionCreator->addAction($id_e,$authentification->getId(),Action::MODIFICATION,"Modification du document");
}


$titre_field = $formulaire->getTitreField();
$titre = $donneesFormulaire->get($titre_field);

$document->setTitre($id_d,$titre);

if ($donneesFormulaire->hasOnChangeHook()){
	require_once(PASTELL_PATH."/externaldata/".$donneesFormulaire->hasOnChangeHook());
	exit;
}

if ( $recuperateur->get('ajouter') ){
	header("Location: edition.php?id_d=$id_d&id_e=$id_e&page=$page");
	exit;
}
if ( $recuperateur->get('suivant') ){
	header("Location: edition.php?id_d=$id_d&id_e=$id_e&page=".($page+1));
	exit;
}

if ($recuperateur->get('precedent')){
	header("Location: edition.php?id_d=$id_d&id_e=$id_e&page=".($page - 1));
	exit;
}

header("Location: " . SITE_BASE . "document/detail.php?id_d=$id_d&id_e=$id_e&page=$page");
