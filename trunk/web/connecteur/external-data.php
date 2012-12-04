<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");

$recuperateur = new Recuperateur($_GET);
$field = $recuperateur->get('field');
$id_ce = $recuperateur->get('id_ce');

$connecteur_info = $objectInstancier->ConnecteurEntiteSQL->getInfo($id_ce);
$id_e  = $connecteur_info['id_e'];

if ( ! $roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$id_e)) {
	$lastError->setLastError("Vous n'avez pas le droit de faire cette action (entite:edition)");
	header("Location: edition-properties.php?id_e=$id_e&page=$page");
	exit;
}

$documentType = $documentTypeFactory->getEntiteDocumentType($connecteur_info['id_connecteur']);
$formulaire = $documentType->getFormulaire();
$script = $formulaire->getField($field)->getProperties('script');


require_once(PASTELL_PATH . "/externaldata/$script");
