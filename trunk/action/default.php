<?php

require_once (PASTELL_PATH . "/lib/action/DocumentAction.class.php");


$documentAction = new DocumentAction($sqlQuery,$journal,$id_d,$id_e,$authentification->getId());
$id_a = $documentAction->addAction($action);

$documentActionEntite = new DocumentActionEntite($sqlQuery);
$documentActionEntite->addAction($id_a,$id_e,$journal);

$lastMessage->setLastMessage("L'action $action a été executé sur le document");
	
header("Location: detail.php?id_d=$id_d&id_e=$id_e");