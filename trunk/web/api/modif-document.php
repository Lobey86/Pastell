<?php
require_once("init-api.php");

$recuperateur = new Recuperateur($_REQUEST);
$data = $recuperateur->getAll();

$fileUploader = new FileUploader($_FILES);

$api_json->modifDocument($data,$fileUploader);
