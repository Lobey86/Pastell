#! /usr/bin/php
<?php
require_once( dirname(__FILE__) . "/../web/init.php");


$entiteListe = new EntiteListe($sqlQuery);

$liste_collectivite = $entiteListe->getAll('collectivite');

$zenMail = new ZenMail();
$notification = new Notification($sqlQuery);
$notificationMail = new NotificationMail($notification,$zenMail,$journal);

foreach($liste_collectivite as $col){
	try {
		$tdT = $objectInstancier->ConnecteurFactory->getConnecteurByType($col['id_e'],'actes','TdT');
		if (!$tdT){
			echo "{$col['denomination']} : aucun connecteur TdT pour actes\n";
			continue;
		}

		if ($tdT->verifClassif()){
			echo "{$col['denomination']} : la classification est à jour\n";
			continue;
		}
		$result = $tdT->getClassification();
			
		$donneesFormulaire = $objectInstancier->ConnecteurFactory->getConnecteurConfigByType($col['id_e'],'actes','TdT');
		$donneesFormulaire->addFileFromData("classification_file","classification.xml",$result);
					
		$objectInstancier->ChoixClassificationControler->disabledClassificationCDG($col['id_e']);
		
		$message = "{$col['denomination']} : classification  mise à jour\n";
		$notificationMail->notify($col['id_e'],$col['id_d'],'recup-classification','collectivite-properties',$message);
		
		echo $message;
			
		
	} catch(Exception $e){
		echo  "{$col['denomination']} : ".$e->getMessage()."\n";
	}
	
}





