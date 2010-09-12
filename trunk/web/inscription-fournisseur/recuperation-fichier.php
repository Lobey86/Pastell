<?php

require_once("init-information.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");

$recuperateur = new Recuperateur($_GET);
$field = $recuperateur->get('field');

$file_path = $donneesFormulaire->getFilePath($field);
$file_name = $donneesFormulaire->get($field);

if (! file_exists($file_path)){
	$lastError->setLastError("Ce fichier n'existe pas");
	header("Location: index.php");
	exit;
}

header("Content-type: ".mime_content_type($file_path));
header("Content-disposition: attachment; filename=$file_name");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Pragma: public");

readfile($file_path);