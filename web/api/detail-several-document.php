<?php
require_once("init-api.php");
require_once (PASTELL_PATH . "/lib/action/ActionPossibleFactory.class.php");

$recuperateur = new Recuperateur($_REQUEST);
$all_id_d = $recuperateur->get('id_d');
$id_e = $recuperateur->get('id_e');

$document = new Document($sqlQuery);

foreach($all_id_d as $id_d) {
	$info = $document->getInfo($id_d);
	$result[$id_d]['info'] = $info;
	
	if ( ! $roleUtilisateur->hasDroit($id_u,$info['type'].":edition",$id_e)) {
		$JSONoutput->displayErrorAndExit("Acces interdit id_e=$id_e, id_d=$id_d,id_u=$id_u");
	}
	$donneesFormulaire  = $donneesFormulaireFactory->get($id_d,$info['type']);
	
	$actionPossibleFactory = new ActionPossibleFactory($sqlQuery);
	$actionPossible = $actionPossibleFactory->getInstance($id_u,$id_e,$id_d,$info['type'],$donneesFormulaire,$roleUtilisateur);
	
	$result[$id_d]['data'] = $donneesFormulaire->getRawData();
	$result[$id_d]['action_possible'] = $actionPossible->getActionPossible($id_d);
} 

$JSONoutput->display($result);