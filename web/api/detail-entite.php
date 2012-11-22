<?php
require_once("init-api.php");

$recuperateur = new Recuperateur($_REQUEST);
$id_e = $recuperateur->get('id_e');

$document = new Document($sqlQuery);


if ( ! $roleUtilisateur->hasDroit($id_u,"entite:lecture",$id_e)) {
	$JSONoutput->displayErrorAndExit("Acces interdit id_e=$id_e, id_u=$id_u");	
}

$documentType = $documentTypeFactory->getDocumentType('collectivite-properties');
$formulaire = $documentType->getFormulaire();

$donneesFormulaire = $donneesFormulaireFactory->get($id_e,'collectivite-properties');

$result['data'] = $donneesFormulaire->getRawData();


$JSONoutput->display($result);