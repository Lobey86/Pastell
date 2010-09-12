<?php
require_once("init-authenticated.php");

require_once( ZEN_PATH . "/lib/Recuperateur.class.php");
require_once (PASTELL_PATH . "/lib/formulaire/Formulaire.class.php");
require_once( PASTELL_PATH . "/lib/formulaire/DonneesFormulaire.class.php");
require_once( PASTELL_PATH . "/lib/transaction/message/MessageRessource.class.php");

$recuperateur = new Recuperateur($_GET);
$id = $recuperateur->get('id');
$field = $recuperateur->get('field');
$file_path = false;

if ($id){
	$messageRessource = new MessageRessource($sqlQuery,$id);
	$info = $messageRessource->getInfo();
	if ($info['type'] == 'file'){
		$file_path = $info['ressource'];
		$file_name = $info['original_name'];
	} else {
		$donneesFormulaire = new DonneesFormulaire($info['ressource']);
		$file_path = $donneesFormulaire->getFilePath($field);
		$file_name = $donneesFormulaire->get($field);
	}
} else {
	require_once( PASTELL_PATH . "/web/inscription-fournisseur/init-information.php");
	$file_path = $donneesFormulaire->getFilePath($field);
	$file_name = $donneesFormulaire->get($field);
}

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