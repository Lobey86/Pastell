<?php
require_once("init-api.php");

$recuperateur = new Recuperateur($_REQUEST);
$all_id_d = $recuperateur->get('id_d');
$id_e = $recuperateur->get('id_e');

$api_json->detailSeveralDocument($id_e, $all_id_d);

