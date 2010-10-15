<?php

require_once( PASTELL_PATH . "/lib/system/Tedetis.class.php");

$collectviteProperties = new DonneesFormulaire(WORKSPACE_PATH  . "/$id_e.yml");

$tedetis = new Tedetis($collectviteProperties);


if (!  $tedetis->postActes($donneesFormulaire) ){
	$lastError->setLastError( $tedetis->getLastError());
	header("Location: detail.php?id_d=$id_d&id_e=$id_e");
	exit;
}


$actionCreator = new ActionCreator($sqlQuery,$journal,$id_d);
$actionCreator->addAction($id_e,$authentification->getId(),$action,"Le document a été envoyé au contrôle de légalité");
	

$lastMessage->setLastMessage("Le document a été envoyé au contrôle de légalité");
	
header("Location: detail.php?id_d=$id_d&id_e=$id_e");