<?php
require_once( dirname(__FILE__) . "/../init-authenticated.php");


$recuperateur = new Recuperateur($_POST);

$id_e = $recuperateur->getInt('id_e',0);
$centre_de_gestion = $recuperateur->getInt('centre_de_gestion');

if ( ! $roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$id_e) ) {
	header("Location: " . SITE_BASE ."index.php");
	exit;
}

$fileUploader = new FileUploader($_FILES);
$file_path = $fileUploader->getFilePath('csv_col');
if (! $file_path){
	$lastError->setLastError("Impossible de lire le fichier");
	header("Location: import.php?id_e=$id_e");
	exit;	
}

$CSV = new CSV();
$colList = $CSV->get($file_path);

$entiteCreator = new EntiteCreator($sqlQuery,$journal);
$nb_col = 0;
foreach($colList as $col){
	$the_id_e = $entiteCreator->create(0,$col[1],$col[0],Entite::TYPE_COLLECTIVITE,$id_e,$centre_de_gestion);
	$nb_col++;
}

$lastMessage->setLastMessage("$nb_col collectivités ont été créées");
header("Location: index.php");
