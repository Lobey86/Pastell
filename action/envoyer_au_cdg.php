<?php

require_once( PASTELL_PATH . "/lib/entite/EntiteRelation.class.php");
require_once (PASTELL_PATH . "/lib/action/DocumentAction.class.php");
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

$documentAction = new DocumentAction($sqlQuery,$journal,$id_d,$id_e,$id_u);
$id_a = $documentAction->addAction('send-cdg');



$documentActionEntite = new DocumentActionEntite($sqlQuery);
$documentActionEntite->addAction($id_a,$id_e,$journal);

$documentActionCDG = new DocumentAction($sqlQuery,$journal,$id_d,$id_cdg,0);
$id_a = $documentActionCDG->addAction('recu-cdg');
$documentActionEntite->addAction($id_a,$id_e,$journal);
$documentActionEntite->addAction($id_a,$id_cdg,$journal);

$message =  "La transaction $id_d est passé dans l'état :  " . $theAction->getActionName('send-cdg');
$message .= "\n\n";

$notificationMail->notify($id_cdg,$id_d,'recu-cdg', 'rh-actes',$message);


$entiteProperties = new EntiteProperties($sqlQuery,$id_cdg);
$has_ged = $entiteProperties->getProperties(EntiteProperties::ALL_FLUX,'has_ged');
if ($has_ged == 'auto'){	
	$id_a = $documentActionCDG->addAction('send-ged');
	$documentActionEntite->addAction($id_a,$id_cdg,$journal);
	
}

$has_archivage = $entiteProperties->getProperties(EntiteProperties::ALL_FLUX,'has_archivage');
if ($has_archivage == 'auto'){	
	$id_a = $documentActionCDG->addAction('send-archive');
	$documentActionEntite->addAction($id_a,$id_cdg,$journal);
}


$lastMessage->setLastMessage("Le document a été envoyé à votre centre de gestion");
	

header("Location: detail.php?id_d=$id_d&id_e=$id_e");