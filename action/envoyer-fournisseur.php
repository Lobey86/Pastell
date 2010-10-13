<?php

$id_fournisseur = $recuperateur->get('destinataire');

if (! $id_fournisseur){
	header("Location: " . SITE_BASE . "/entite/choix-entite.php?id_d=$id_d&id_e=$id_e&action=$action&type=".Entite::TYPE_FOURNISSEUR);
	exit;
}


$entite = new Entite($sqlQuery,$id_e);
$infoEntite = $entite->getInfo();

$documentEntite = new DocumentEntite($sqlQuery);
$documentActionEntite = new DocumentActionEntite($sqlQuery);

foreach($id_fournisseur as $id_four) {
	
	$documentEntite->addRole($id_d,$id_four,"lecteur");
	
	$entiteCollectivite = new Entite($sqlQuery,$id_four);
	$infoCollectivite = $entiteCollectivite->getInfo();
	$denomination_four = $infoCollectivite['denomination']; 	
	
	$actionCreator = new ActionCreator($sqlQuery,$journal,$id_d);
	
	$actionCreator->addAction($id_e,$authentification->getId(),'envoi-fournisseur', "Le document a été envoyé  à $denomination_four");
	$actionCreator->addToEntite($id_four,"Le document a été envoyé par " . $infoEntite['denomination']);
	
	
	$actionCreator->addAction($id_four,0,'recu-fournisseur', "Le document a été reçu");
	$actionCreator->addToEntite($id_e,"Le document a été reçu par $denomination_four");
	
	
	$notificationMail->notify($id_four,$id_d,'envoie', 'fournisseur-message',"Votre centre de gestion vous envoi un nouveau message");

}

$lastMessage->setLastMessage("Le document a été envoyé au(x) fournisseur(s) selectionnée(s)");

header("Location: detail.php?id_d=$id_d&id_e=$id_e");