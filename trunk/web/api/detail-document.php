<?php
require_once("init-api.php");
require_once (PASTELL_PATH . "/lib/action/ActionPossibleFactory.class.php");

$recuperateur = new Recuperateur($_REQUEST);
$id_d = $recuperateur->get('id_d');
$id_e = $recuperateur->get('id_e');

$document = new Document($sqlQuery);
$info = $document->getInfo($id_d);
$result['info'] = $info;

if ( ! $roleUtilisateur->hasDroit($id_u,$info['type'].":edition",$id_e)) {
	$JSONoutput->displayErrorAndExit("Acces interdit id_e=$id_e, id_d=$id_d,id_u=$id_u");
}
$donneesFormulaire  = $donneesFormulaireFactory->get($id_d,$info['type']);

$actionPossibleFactory = new ActionPossibleFactory($sqlQuery);
$actionPossible = $actionPossibleFactory->getInstance($id_u,$id_e,$id_d,$info['type'],$donneesFormulaire,$roleUtilisateur);

$result['data'] = $donneesFormulaire->getRawData();
$result['action-possible'] = $actionPossible->getActionPossible($id_d);
$result['action_possible'] = $result['action-possible']; 

$JSONoutput->display($result);