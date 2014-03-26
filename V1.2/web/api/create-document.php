<?php
require_once("init-api.php");

$recuperateur = new Recuperateur($_REQUEST);
$id_e = $recuperateur->getInt('id_e',0);
$type = $recuperateur->get('type');

$api_json->createDocument($id_e, $type);

