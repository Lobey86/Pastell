<?php
require_once("init-api.php");

$recuperateur = new Recuperateur($_REQUEST);
$id_e = $recuperateur->getInt('id_e');
$flux = $recuperateur->get('flux');
$type = $recuperateur->get('type');

$api_json->listFluxConnecteur($id_e, $flux, $type);

?>