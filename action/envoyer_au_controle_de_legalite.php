<?php

$actionCreator = new ActionCreator($sqlQuery,$journal,$id_d);
$actionCreator->addAction($id_e,$authentification->getId(),$action,"Le document a été envoyé au contrôle de légalité");
	

$lastMessage->setLastMessage("Le document a été envoyé au contrôle de légalité");
	
header("Location: detail.php?id_d=$id_d&id_e=$id_e");