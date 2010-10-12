<?php

require_once(dirname(__FILE__)."/../init-authenticated.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");

require_once (PASTELL_PATH . "/lib/document/Document.class.php");

require_once (PASTELL_PATH . "/lib/formulaire/Formulaire.class.php");
require_once( PASTELL_PATH . "/lib/formulaire/DonneesFormulaire.class.php");

$recuperateur = new Recuperateur($_POST);
$id_d = $recuperateur->get('id_d');
$id_e = $recuperateur->get('id_e');
$field = $recuperateur->get('field');
$page = $recuperateur->get('page');



$document = new Document($sqlQuery);

$info = $document->getInfo($id_d);
$type = $info['type'];
$titre = $info['titre'];

if ( ! $roleUtilisateur->hasDroit($authentification->getId(),$type.":edition",$id_e)) {
	$lastError->setLastError("Vous n'avez pas le droit de faire cette action ($type:edition)");
	header("Location: edition.php?id_d=$id_d&id_e=$id_e");
	exit;
}


$documentType = $documentTypeFactory->getDocumentType($type);
$formulaire = $documentType->getFormulaire();
$formulaire->setTabNumber($page);

$theField = $formulaire->getField($field);

$script = $theField->getProperties('script-controler');

require_once(PASTELL_PATH . "/externaldata/$script");

