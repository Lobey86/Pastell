<?php

require_once( PASTELL_PATH . "/lib/entite/EntiteRelation.class.php");
require_once (PASTELL_PATH . "/lib/action/DocumentAction.class.php");


$id_collectivite = $recuperateur->get('destinataire');

if (! $id_collectivite){
	header("Location: " . SITE_BASE . "/entite/choix-collectivite.php?id_d=$id_d&id_e=$id_e");
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
	$id_a = $documentAction->addAction('Envoyer');
	
	$documentActionEntite = new DocumentActionEntite($sqlQuery);
	$documentActionEntite->addAction($id_a,$id_e,$journal,$notificationMail,$message_journal);
	$documentActionEntite->addAction($id_a,$id_col,$journal,$notificationMail,$message_journal);
}

header("Location: detail.php?id_d=$id_d&id_e=$id_e");