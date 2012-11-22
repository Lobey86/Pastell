<?php


require_once(dirname(__FILE__)."/../init-authenticated.php");


require_once (PASTELL_PATH . "/lib/document/Document.class.php");

require_once (PASTELL_PATH . "/lib/formulaire/Formulaire.class.php");
require_once( PASTELL_PATH . "/lib/formulaire/DonneesFormulaire.class.php");

$recuperateur = new Recuperateur($_GET);
$id_d = $recuperateur->get('id_d');
$id_e = $recuperateur->get('id_e');
$field = $recuperateur->get('field');
$page = $recuperateur->get('page');

if ( ! $roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$id_e)) {
	$lastError->setLastError("Vous n'avez pas le droit de faire cette action (entite:edition)");
	header("Location: edition-properties.php?id_e=$id_e&page=$page");
	exit;
}

$documentType = $documentTypeFactory->getDocumentType("collectivite-properties");
$formulaire = $documentType->getFormulaire();

$theField = $formulaire->getField($field);

$script = $theField->getProperties('script');

require_once(PASTELL_PATH . "/externaldata/$script");
