<?php
require_once("init-api.php");

$recuperateur = new Recuperateur($_REQUEST);
$id_e = $recuperateur->getInt('id_e',0);
$id_d = $recuperateur->get('id_d');
$action = $recuperateur->get('action');
$id_destinataire = $recuperateur->get('destinataire');
if (!$id_destinataire){
	$id_destinataire = array();
}
$action_params = $recuperateur->get('action_params');
if (!$action_params) {
	$action_params=array();
}

$api_json->action($id_e, $id_d,$action,$id_destinataire, true, $action_params);
