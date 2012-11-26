<?php
require_once("init-api.php");

$recuperateur = new Recuperateur($_REQUEST);
$id_e = $recuperateur->get('id_e');

$document = $objectInstancier->Document;


if ( ! $roleUtilisateur->hasDroit($id_u,"entite:lecture",$id_e)) {
	$JSONoutput->displayErrorAndExit("Acces interdit id_e=$id_e, id_u=$id_u");	
}

$documentType = $documentTypeFactory->getEntiteConfig($id_e);
$formulaire = $documentType->getFormulaire();

$donneesFormulaire = $donneesFormulaireFactory->getEntiteFormulaire($id_e);

$result['data'] = $donneesFormulaire->getRawData();


$JSONoutput->display($result);