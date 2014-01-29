<?php

require_once("init-api.php");

$recuperateur = new Recuperateur($_REQUEST);
$data = $recuperateur->getAll();

$fileUploader = new FileUploader();

$api_json->createUtilisateur($data, $fileUploader);

?>
