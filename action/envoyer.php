<?php

require_once( PASTELL_PATH . "/lib/entite/EntiteRelation.class.php");
require_once (PASTELL_PATH . "/lib/action/DocumentAction.class.php");


$id_collectivite = $recuperateur->get('destinataire');

if (! $id_collectivite){
	header("Location: " . SITE_BASE . "/entite/choix-collectivite.php?id_d=$id_d&id_e=$id_e&action=$action");
	exit;
}

$entite = new Entite($sqlQuery,$id_e);
$infoEntite = $entite->getInfo();
$documentEntite = new DocumentEntite($sqlQuery);

	
foreach($id_collectivite as $id_col) {
	
	$documentEntite->addRole($id_d,$id_col,"lecteur");
	
	$documentAction = new DocumentAction($sqlQuery,$journal,$id_d,$id_e,$authentification->getId());
	
	$entiteCollectivite = new Entite($sqlQuery,$id_col);
	$infoCollectivite = $entiteCollectivite->getInfo();
	$message_journal = "Envoyé à " . $infoCollectivite['denomination']; 	
	$id_a = $documentAction->addAction('envoie');
	
	$documentActionEntite = new DocumentActionEntite($sqlQuery);
	$documentActionEntite->addAction($id_a,$id_e,$journal,$message_journal);
	$documentActionEntite->addAction($id_a,$id_col,$journal,$message_journal);
	
	$notificationMail->notify($id_e,$id_d,'envoie', 'rh-messages',"Votre centre de gestion vous envoie un nouveau message");

}

$lastMessage->setLastMessage("Le document a été envoyé au(x) collectivité(s) selectionnée(s)");

header("Location: detail.php?id_d=$id_d&id_e=$id_e");