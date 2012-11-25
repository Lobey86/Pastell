<?php
require_once("init-api.php");

$recuperateur = new Recuperateur($_REQUEST);
$id_d = $recuperateur->get('id_d');
$id_e = $recuperateur->get('id_e');

$document = $objectInstancier->Document;
$info = $document->getInfo($id_d);
$result['info'] = $info;

if ( ! $roleUtilisateur->hasDroit($id_u,$info['type'].":edition",$id_e)) {
	$JSONoutput->displayErrorAndExit("Acces interdit id_e=$id_e, id_d=$id_d,id_u=$id_u");
}
$donneesFormulaire  = $donneesFormulaireFactory->get($id_d,$info['type']);

$actionPossible = $objectInstancier->ActionPossible;

$result['data'] = $donneesFormulaire->getRawData();
$result['action-possible'] = $actionPossible->getActionPossible($id_e,$id_u,$id_d);
$result['action_possible'] = $result['action-possible']; 

$JSONoutput->display($result);