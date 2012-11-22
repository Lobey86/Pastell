<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/FileUploader.class.php");

require_once (PASTELL_PATH . "/lib/formulaire/Formulaire.class.php");
require_once( PASTELL_PATH . "/lib/formulaire/DonneesFormulaire.class.php");
require_once (PASTELL_PATH . "/lib/action/DocumentActionEntite.class.php");

require_once (PASTELL_PATH . "/lib/document/Document.class.php");
require_once (PASTELL_PATH . "/lib/action/DocumentActionEntite.class.php");

require_once (PASTELL_PATH . "/lib/notification/Notification.class.php");
require_once (PASTELL_PATH . "/lib/notification/NotificationMail.class.php");

require_once (PASTELL_PATH . "/lib/document/DocumentEntite.class.php");
require_once (PASTELL_PATH . "/lib/action/ActionPossible.class.php");

//Récupération des données
$recuperateur = new Recuperateur($_POST);
$id_d = $recuperateur->get('id_d');
$page = $recuperateur->get('page');
$id_e = $recuperateur->get('id_e');
$field = $recuperateur->get('field');
$num = $recuperateur->getInt('num',0);


$document = new Document($sqlQuery);
$info = $document->getInfo($id_d);

$type = $info['type'];

if ( ! $roleUtilisateur->hasDroit($authentification->getId(),$type.":edition",$id_e)) {
	header("Location: list.php");
	exit;
}


$entite = new Entite($sqlQuery,$id_e);
$documentType = $documentTypeFactory->getDocumentType($type);
$theAction = $documentType->getAction();
$donneesFormulaire = $donneesFormulaireFactory->get($id_d,$type);

$actionPossible = new ActionPossible($sqlQuery,$id_e,$authentification->getId(),$theAction);
$actionPossible->setRoleUtilisateur($roleUtilisateur);
$actionPossible->setDonnesFormulaire($donneesFormulaire);
$actionPossible->setEntite($entite);
//$actionPossible->setHeritedProperties($collectiviteProperties);


if ( ! $actionPossible->isActionPossible($id_d,'modification') ) {
	$lastError->setLastError("L'action « modification »  n'est pas permise : " .$actionPossible->getLastBadRule() );
	header("Location: detail.php?id_d=$id_d&id_e=$id_e&page=$page");
	exit;
}


$documentType = $documentTypeFactory->getDocumentType($type);
$formulaire = $documentType->getFormulaire();
$formulaire->setTabNumber($page);


$donneesFormulaire = $donneesFormulaireFactory->get($id_d,$type);
$donneesFormulaire->removeFile($field,$num);


header("Location: " . SITE_BASE . "document/edition.php?id_d=$id_d&id_e=$id_e&page=$page");
