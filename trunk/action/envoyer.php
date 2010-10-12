<?php

$id_collectivite = $recuperateur->get('destinataire');

if (! $id_collectivite){
	header("Location: " . SITE_BASE . "/entite/choix-collectivite.php?id_d=$id_d&id_e=$id_e&action=$action");
	exit;
}

$entite = new Entite($sqlQuery,$id_e);
$infoEntite = $entite->getInfo();
$documentEntite = new DocumentEntite($sqlQuery);

$documentActionEntite = new DocumentActionEntite($sqlQuery);

foreach($id_collectivite as $id_col) {
	
	$documentEntite->addRole($id_d,$id_col,"lecteur");
	$entiteCollectivite = new Entite($sqlQuery,$id_col);
	$infoCollectivite = $entiteCollectivite->getInfo();
	$denomination_col = $infoCollectivite['denomination']; 	
	
	
	$actionCreator = new ActionCreator($sqlQuery,$journal,$id_d);
	
	$actionCreator->addAction($id_e,$authentification->getId(),'envoi-col', "Le document a été envoyé  à $denomination_col");
	$actionCreator->addToEntite($id_col,"Le document a été envoyé par le centre de gestion");
	
	
	$actionCreator->addAction($id_col,0,'recu-col', "Le document a été reçu");
	$actionCreator->addToEntite($id_e,"Le document a été reçu par $denomination_col");
	

	
	$notificationMail->notify($id_col,$id_d,'envoie', 'rh-messages',"Votre centre de gestion vous envoi un nouveau message");

}

$lastMessage->setLastMessage("Le document a été envoyé au(x) collectivité(s) selectionnée(s)");

header("Location: detail.php?id_d=$id_d&id_e=$id_e");