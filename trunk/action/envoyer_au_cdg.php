<?php

require_once( PASTELL_PATH . "/lib/entite/EntiteRelation.class.php");
require_once (PASTELL_PATH . "/lib/action/DocumentAction.class.php");


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

$documentAction = new DocumentAction($sqlQuery,$journal,$id_d,$id_e,$authentification->getId());
$id_a = $documentAction->addAction('send-cdg');

$documentActionEntite = new DocumentActionEntite($sqlQuery);
$documentActionEntite->addAction($id_a,$id_e,$journal);
$documentActionEntite->addAction($id_a,$id_cdg,$journal);

$message =  "La transaction $id_d est passé dans l'état :  " . $theAction->getActionName($action);
$message .= "\n\n";

$notificationMail->notify($id_cdg,$id_d,'send-cdg', 'rh-actes',$message);

$lastMessage->setLastMessage("Le document a été envoyé à votre centre de gestion");
	

header("Location: detail.php?id_d=$id_d&id_e=$id_e");