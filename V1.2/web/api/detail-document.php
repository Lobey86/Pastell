<?php
require_once("init-api.php");

$recuperateur = new Recuperateur($_REQUEST);
$id_e = $recuperateur->get('id_e');
$id_d = $recuperateur->get('id_d');

$api_json->detailDocument($id_e, $id_d);
