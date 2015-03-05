<?php
require_once("init-api.php");

$recuperateur = new Recuperateur($_REQUEST);

$id_e = $recuperateur->get('id_e');

$document = $objectInstancier->Document;


if ( ! $roleUtilisateur->hasDroit($id_u,"entite:lecture",$id_e)) {
	$JSONoutput->displayErrorAndExit("Acces interdit id_e=$id_e, id_u=$id_u");
}

$result = array();
$all_connecteur = $objectInstancier->ConnecteurEntiteSQL->getAll($id_e);
foreach($all_connecteur as $connecteur){
	$documentType = $objectInstancier->DocumentTypeFactory->getEntiteDocumentType($connecteur['id_connecteur']);

	$formulaire = $documentType->getFormulaire();
	$donneesFormulaire = $donneesFormulaireFactory->getConnecteurEntiteFormulaire($connecteur['id_ce']);
	$result[$connecteur['libelle']] = $donneesFormulaire->getRawData();
}



$JSONoutput->display($result);


