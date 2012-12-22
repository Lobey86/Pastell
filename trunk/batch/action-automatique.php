#! /usr/bin/php
<?php
$start = time();
$min_exec_time = 60;

require_once( __DIR__ . "/../web/init.php");

$zenMail = new ZenMail($zLog);
$notification = new Notification($sqlQuery);
$notificationMail = new NotificationMail($notification,$zenMail,$journal);

$documentEntite = new DocumentEntite($sqlQuery);



foreach($objectInstancier->fluxDefinitionFiles->getAll() as $type => $config){
	$tabAction = $objectInstancier->DocumentTypeFactory->getFluxDocumentType($type)->getAction()->getAutoAction();
	foreach($tabAction as $etat_actuel => $etat_cible){	
		foreach ($documentEntite->getFromAction($type,$etat_actuel) as $infoDocument){
			echo "Traitement de ({$infoDocument['id_e']},{$infoDocument['id_d']},{$infoDocument['type']},$etat_actuel->$etat_cible) : ";
			$result = $objectInstancier->ActionExecutorFactory->executeOnDocument($infoDocument['id_e'],0,$infoDocument['id_d'],$etat_cible,array(),true);
			if (! $result){
				echo  $objectInstancier->ActionExecutorFactory->getLastMessage();
			}
			echo "\n";
		}
	}
}

$all_connecteur = $objectInstancier->ConnecteurEntiteSQL->getAll(0);

foreach($all_connecteur as $connecteur){
	echo "Connecteur {$connecteur['libelle']} : ";
	$documentType = $objectInstancier->DocumentTypeFactory->getGlobalDocumentType($connecteur['id_connecteur']);
	$all_action = $documentType->getAction()->getAutoAction();
	foreach($all_action as $action){
		$result = $objectInstancier->ActionExecutorFactory->executeOnConnecteur($connecteur['id_ce'],0,$action);
		if (!$result){
			echo  $objectInstancier->ActionExecutorFactory->getLastMessage();
		} else {
			echo "ok";
		}
		
	}
	echo "\n";
}

$objectInstancier->LastUpstart->updateMtime();

$stop = time();
$sleep = $min_exec_time - ($stop -$start);
if ($sleep > 0){
	echo "Arret du script : $sleep";
	sleep($sleep);
}
