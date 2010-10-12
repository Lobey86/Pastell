<?php



$documentEntite = new DocumentEntite($sqlQuery);
$id_ged = $documentEntite->getEntiteWithRole($id_d,"editeur");

$actionCreator = new ActionCreator($sqlQuery,$journal,$id_d);
$actionCreator->addAction($id_e,$authentification->getId(),$action, "Vous avez accusé réception de ce message");
$actionCreator->addToEntite($id_ged,"Un accusé de réception a été recu pour le document");

$message = "Un accusé de réception a été recu pour le document $id_d ";
$notificationMail->notify($id_ged,$id_d,$action, 'rh-message',$message);


$lastMessage->setLastMessage("L'accusé de réception a été envoyé au centre de gestion");

header("Location: detail.php?id_d=$id_d&id_e=$id_e");