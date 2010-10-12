<?php


$actionCreator = new ActionCreator($sqlQuery,$journal,$id_d);
$actionCreator->addAction($id_e,$authentification->getId(),$action,"L'action $actionName a été executé sur le document");


$lastMessage->setLastMessage("L'action $actionName a été executé sur le document");
	
header("Location: detail.php?id_d=$id_d&id_e=$id_e");