<?php
require_once("init-api.php");

$recuperateur = new Recuperateur($_REQUEST);
$id_e = $recuperateur->getInt('id_e');
$id_d = $recuperateur->get('id_d');
$field_name = $recuperateur->get('field_name');
$file_name = $recuperateur->get('file_name');
$file_number = $recuperateur->getInt('file_number');
$file_content = $recuperateur->get('file_content');

$api_json->sendFile($id_e, $id_d,$field_name,$file_name,$file_number,$file_content);
