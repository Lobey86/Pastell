<?php
require_once("init-api.php");


$recuperateur = new Recuperateur($_REQUEST);
$id_e = $recuperateur->getInt('id_e',0);
$id_d = $recuperateur->get('id_d');
$field = $recuperateur->get('field');

$api_json->externalData($id_e, $id_d,$field);
