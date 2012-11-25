<?php
require_once("init-api.php");

$recuperateur = new Recuperateur($_REQUEST);
$all_id_d = $recuperateur->get('id_d');
$id_e = $recuperateur->get('id_e');

$document = $objectInstancier->Document;

foreach($all_id_d as $id_d) {
	$info = $document->getInfo($id_d);
	$result[$id_d]['info'] = $info;
	
	if ( ! $roleUtilisateur->hasDroit($id_u,$info['type'].":edition",$id_e)) {
		$JSONoutput->displayErrorAndExit("Acces interdit id_e=$id_e, id_d=$id_d,id_u=$id_u");
	}
	$donneesFormulaire  = $donneesFormulaireFactory->get($id_d,$info['type']);
	
	$actionPossible = $objectInstancier->ActionPossible;
		
	$result[$id_d]['data'] = $donneesFormulaire->getRawData();
	$result[$id_d]['action_possible'] = $actionPossible->getActionPossible($id_e,$id_u,$id_d);
} 

$JSONoutput->display($result);