<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");

require_once( PASTELL_PATH . "/lib/FileUploader.class.php");

require_once (PASTELL_PATH . "/lib/formulaire/Formulaire.class.php");
require_once( PASTELL_PATH . "/lib/formulaire/DonneesFormulaire.class.php");
require_once (PASTELL_PATH . "/lib/document/Document.class.php");



//Récupération des données
$recuperateur = new Recuperateur($_POST);
$id_d = $recuperateur->get('id_d');
$form_type = $recuperateur->get('form_type');


$formulaire_file = PASTELL_PATH . "/form/$form_type.yml" ;
if ( ! file_exists($formulaire_file)){
	header("Location: index.php");
	exit;
}
$formulaire = new Formulaire( $formulaire_file );
$formulaire->setTabNumber(0);


$document = new Document($sqlQuery);

$info = $document->getInfo($id_d);
if (! $info){
	$document->save($id_d,$form_type);
}

$fileUploader = new FileUploader($_FILES);


$donneesFormulaire = new DonneesFormulaire( WORKSPACE_PATH  . "/$id_d.yml");
$donneesFormulaire->setFormulaire($formulaire);
	

$donneesFormulaire->save($recuperateur,$fileUploader);

foreach($fileUploader->getAll() as $filename => $orig_filename){
	$url = WORKSPACE_PATH . "/$id_d" . "_" . $filename;
	$fileUploader->save($filename,$url);
}


header("Location: " . SITE_BASE . "document/voir.php?id_d=$id_d");
