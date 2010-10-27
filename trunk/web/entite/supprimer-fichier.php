<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");

require_once( PASTELL_PATH . "/lib/FileUploader.class.php");

require_once (PASTELL_PATH . "/lib/formulaire/Formulaire.class.php");
require_once( PASTELL_PATH . "/lib/formulaire/DonneesFormulaire.class.php");
require_once (PASTELL_PATH . "/lib/action/DocumentActionEntite.class.php");

require_once (PASTELL_PATH . "/lib/document/Document.class.php");
require_once (PASTELL_PATH . "/lib/action/DocumentActionEntite.class.php");

require_once (PASTELL_PATH . "/lib/base/ZenMail.class.php");
require_once (PASTELL_PATH . "/lib/notification/Notification.class.php");
require_once (PASTELL_PATH . "/lib/notification/NotificationMail.class.php");

require_once (PASTELL_PATH . "/lib/document/DocumentEntite.class.php");

//Récupération des données
$recuperateur = new Recuperateur($_POST);
$page = $recuperateur->get('page');
$id_e = $recuperateur->get('id_e');
$field = $recuperateur->get('field');



if ( ! $roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$id_e)) {
	header("Location: list.php");
	exit;
}

$documentType = $documentTypeFactory->getDocumentType('collectivite-properties');
$formulaire = $documentType->getFormulaire();
$formulaire->setTabNumber($page);

$donneesFormulaire = $donneesFormulaireFactory->get($id_e,'collectivite-properties');
$donneesFormulaire->removeFile($field);


header("Location: " . SITE_BASE . "entite/edition-properties.php?id_e=$id_e&page=$page");
