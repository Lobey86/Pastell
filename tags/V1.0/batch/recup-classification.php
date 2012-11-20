#! /usr/bin/php
<?php
require_once( dirname(__FILE__) . "/../web/init.php");
require_once( PASTELL_PATH . "/lib/connecteur/tedetis/Tedetis.class.php");
require_once( PASTELL_PATH . "/lib/base/ZenMail.class.php");
require_once( PASTELL_PATH . "/lib/notification/Notification.class.php");
require_once( PASTELL_PATH . "/lib/notification/NotificationMail.class.php");


$entiteListe = new EntiteListe($sqlQuery);

$liste_collectivite = $entiteListe->getAll('collectivite');

$zenMail = new ZenMail($zLog);
$notification = new Notification($sqlQuery);
$notificationMail = new NotificationMail($notification,$zenMail,$journal);


foreach($liste_collectivite as $col){
	
	echo $col['denomination'] .": ";
	$donneesFormulaire = $donneesFormulaireFactory->get($col['id_e'],'collectivite-properties');
	if ( $donneesFormulaire->get('tdt_activate')) {
		$tedetis = TedetisFactory::getInstance($donneesFormulaire);
		
		if (! $tedetis->verifClassif()){
			echo "la classification n'est pas à jour";
			$result = $tedetis->getClassification();
		
			if ($result){
				$donneesFormulaire->addFileFromData("classification_file","classification.xml",$result);
				$message = "Classification de la collectivité {$col['denomination']} mise à jour";				
				$objectInstancier->ChoixClassificationControler->disabledClassificationCDG($col['id_e']);
			} else {
				$message =  "Problème lors de la récuperation de la classification de {$col['denomination']}";
			}
			$notificationMail->notify($col['id_e'],$col['id_d'],'recup-classification','récuperation automatique',$message);
			echo $message;
		} else {
			echo "classification OK";
		}
	} else {
		echo " Module TdT desactivé";
	}
	echo "\n";
}





