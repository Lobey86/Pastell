#! /usr/bin/php
<?php
$start = time();
$min_exec_time = 60;


require_once( dirname(__FILE__) . "/../web/init.php");

require_once (PASTELL_PATH . "/lib/document/DocumentTypeFactory.class.php");
require_once (PASTELL_PATH . "/lib/document/DocumentEntite.class.php");
require_once( PASTELL_PATH . "/lib/notification/Notification.class.php");
require_once (PASTELL_PATH . "/lib/journal/Journal.class.php");
require_once (PASTELL_PATH . "/lib/notification/NotificationMail.class.php");
require_once (PASTELL_PATH . "/lib/action/ActionCreator.class.php");
require_once( PASTELL_PATH ."/lib/timestamp/OpensslTSWrapper.class.php");
require_once( PASTELL_PATH ."/lib/timestamp/SignServer.class.php");
require_once( PASTELL_PATH ."/lib/document/Document.class.php");

$signServer = new SignServer(SIGN_SERVER_URL,new OpensslTSWrapper(OPENSSL_PATH,$zLog));

$journal = new Journal($signServer, $sqlQuery,0);

$zenMail = new ZenMail($zLog);
$notification = new Notification($sqlQuery);
$notificationMail = new NotificationMail($notification,$zenMail,$journal);

$documentEntite = new DocumentEntite($sqlQuery);
$documentTypeFactory = new DocumentTypeFactory();


foreach($documentTypeFactory->getAutoAction() as $type => $tabAction){
	foreach($tabAction as $action => $class){	
		foreach ($documentEntite->getFromAction($type,$action) as $infoDocument){

			$id_d = $infoDocument['id_d'];
			$id_e = $infoDocument['id_e'];
			
			$donneesFormulaire = $donneesFormulaireFactory->get($id_d,$infoDocument['type']);
			
			$collectiviteProperties = $donneesFormulaireFactory->get($id_e,'collectivite-properties');
			
			require_once( dirname(__FILE__) . "/../action/$class.class.php" );
			
			$c = new $class($zLog,$sqlQuery,$id_d,$id_e,0,$infoDocument['type']);
			
			$c->setCollectiviteProperties($collectiviteProperties);
			$c->setNotificationMail($notificationMail);
			$c->go(true);			
		}
		
	}
}


$stop = time();
$sleep = $min_exec_time - ($stop -$start);
if ($sleep > 0){
	echo "Arret du script : $sleep";
	sleep($sleep);
}



