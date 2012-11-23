#! /usr/bin/php
<?php
$start = time();
$min_exec_time = 60;

require_once( __DIR__ . "/../web/init.php");
require_once (PASTELL_PATH . "/lib/document/DocumentTypeFactory.class.php");

$zenMail = new ZenMail($zLog);
$notification = new Notification($sqlQuery);
$notificationMail = new NotificationMail($notification,$zenMail,$journal);

$documentEntite = new DocumentEntite($sqlQuery);
$documentTypeFactory = new DocumentTypeFactory();
foreach($documentTypeFactory->getAutoAction() as $type => $tabAction){
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


$stop = time();
$sleep = $min_exec_time - ($stop -$start);
if ($sleep > 0){
	echo "Arret du script : $sleep";
	sleep($sleep);
}



