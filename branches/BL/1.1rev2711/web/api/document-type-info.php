<?php
require_once("init-api.php");

$recuperateur = new Recuperateur($_REQUEST);
$type = $recuperateur->get('type');

$api_json->documentTypeInfo($type);

