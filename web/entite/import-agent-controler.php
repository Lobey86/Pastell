<?php
require_once( dirname(__FILE__) . "/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/base/CSV.class.php");
require_once( PASTELL_PATH . "/lib/entite/AgentSQL.class.php");


$recuperateur = new Recuperateur($_POST);

$id_e = $recuperateur->getInt('id_e');


if ( ! $roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",0) ) {
	header("Location: " . SITE_BASE ."index.php");
	exit;
}
$fileUploader = new FileUploader($_FILES);
$file_path = $fileUploader->getFilePath('csv_agent');
if (! $file_path){
	$lastError->setLastError("Impossible de lire le fichier : " . $fileUploader->getLastError());
	header("Location: import.php?page=1");
	exit;	
}

$CSV = new CSV();
$agentSQL = new AgentSQL($sqlQuery);

$infoCollectivite = array();
if ($id_e){
	$entite = new Entite($sqlQuery,$id_e);
	$infoCollectivite = $entite->getInfo();
}

$fileContent = $CSV->get($file_path);

$nb_agent = 0;
foreach($fileContent as $col){
	if (count($col) != 14){
		continue;
	}
	$agentSQL->add($col,$infoCollectivite);
	$nb_agent++;
}


$lastMessage->setLastMessage("$nb_agent agents ont été créées");
header("Location: import.php?page=1&id_e=$id_e");
