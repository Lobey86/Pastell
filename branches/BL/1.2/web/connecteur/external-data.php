<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");

$recuperateur = new Recuperateur($_GET);
$field = $recuperateur->get('field');
$id_ce = $recuperateur->get('id_ce');

$connecteur_info = $objectInstancier->ConnecteurEntiteSQL->getInfo($id_ce);
$id_e  = $connecteur_info['id_e'];

if ( ! $roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$id_e)) {
	$lastError->setLastError("Vous n'avez pas le droit de faire cette action (entite:edition)");
	header("Location: edition-modif.php?id_ce=$id_ce");
	exit;
}

$documentType = $documentTypeFactory->getEntiteDocumentType($connecteur_info['id_connecteur']);
$formulaire = $documentType->getFormulaire();

$action_name =  $formulaire->getField($field)->getProperties('choice-action');
if ($action_name) {
	$result = $objectInstancier->ActionExecutorFactory->displayChoiceOnConnecteur($id_ce,$authentification->getId(),$action_name,$field);
	if (! $result){
		$lastError->setLastError($objectInstancier->ActionExecutorFactory->getLastMessage());
		header("Location: edition-modif.php?id_ce=$id_ce");
		exit;
		
	}	
} else {
	$script =  $formulaire->getField($field)->getProperties('script');
	require_once(PASTELL_PATH . "/externaldata/$script");
}

