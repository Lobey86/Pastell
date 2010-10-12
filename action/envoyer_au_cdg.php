<?php

require_once( PASTELL_PATH . "/lib/entite/EntiteRelation.class.php");
require_once (PASTELL_PATH . "/lib/entite/EntiteProperties.class.php");


$entite = new Entite($sqlQuery,$id_e);
$infoEntite = $entite->getInfo();

$id_cdg = $infoEntite['centre_de_gestion'];

if (!$id_cdg){
	$lastError->setLastError("La collectivité n'a pas de centre de gestion");
	header("Location: detail.php?id_d=$id_d&id_e=$id_e");
	exit;
}

$documentEntite = new DocumentEntite($sqlQuery);
$documentEntite->addRole($id_d,$id_cdg,"lecteur");

$id_u = 0;

if ($authentification->isConnected()){
	$id_u = $authentification->getId();
} 

$actionCreator = new ActionCreator($sqlQuery,$journal,$id_d);

$actionCreator->addAction($id_e,$id_u,'send-cdg',"Le document a été envoyé au centre de gestion");
$actionCreator->addToEntite($id_cdg,"Le document a été envoyé par la collectivité");

$actionCreator->addAction($id_cdg,0,'recu-cdg',"Le document a été reçu par le centre de gestion");
$actionCreator->addToEntite($id_e,"Le document a été reçu par le centre de gestion");


$message =  "La transaction $id_d est passé dans l'état :  " . $theAction->getActionName('send-cdg');
$message .= "\n\n";

$notificationMail->notify($id_cdg,$id_d,'recu-cdg', 'rh-actes',$message);


$entiteProperties = new EntiteProperties($sqlQuery,$id_cdg);
$has_ged = $entiteProperties->getProperties(EntiteProperties::ALL_FLUX,'has_ged');
if ($has_ged == 'auto'){	
	$actionCreator->addAction($id_cdg,0,'send-ged',"Le document a été déposé dans la GED");
}

$has_archivage = $entiteProperties->getProperties(EntiteProperties::ALL_FLUX,'has_archivage');
if ($has_archivage == 'auto'){	
	$actionCreator->addAction($id_cdg,0,'send-archive',"Le document a été archivé");
}

if ($authentification->isConnected()){
	$lastMessage->setLastMessage("Le document a été envoyé à votre centre de gestion");
	header("Location: detail.php?id_d=$id_d&id_e=$id_e");
}