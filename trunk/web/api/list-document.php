<?php
require_once("init-api.php");

$recuperateur = new Recuperateur($_REQUEST);
$type = $recuperateur->get('type');
$id_e = $recuperateur->get('id_e');
$offset = $recuperateur->getInt('offset',0);
$limit = $recuperateur->getInt('limit',100);


$api_json->listDocument($id_e, $type, $offset, $limit);


