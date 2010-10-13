<?php
require_once( dirname(__FILE__) . "/../web/init.php");

require_once (PASTELL_PATH . "/lib/document/DocumentTypeFactory.class.php");
require_once (PASTELL_PATH . "/lib/document/DocumentEntite.class.php");
require_once( PASTELL_PATH . "/lib/base/ZenMail.class.php");
require_once( PASTELL_PATH . "/lib/notification/Notification.class.php");
require_once (PASTELL_PATH . "/lib/journal/Journal.class.php");
require_once (PASTELL_PATH . "/lib/notification/NotificationMail.class.php");
require_once (PASTELL_PATH . "/lib/action/ActionCreator.class.php");


$journal = new Journal($sqlQuery,0);

$zenMail = new ZenMail($zLog);
$notification = new Notification($sqlQuery);
$notificationMail = new NotificationMail($notification,$zenMail,$journal);

$documentEntite = new DocumentEntite($sqlQuery);
$documentTypeFactory = new DocumentTypeFactory();


foreach($documentTypeFactory->getAutoAction() as $type => $tabAction){
	foreach($tabAction as $action => $script){
		
		
		foreach ($documentEntite->getFromAction($type,$action) as $infoDocument){

			$id_d = $infoDocument['id_d'];
			$id_e = $infoDocument['id_e'];
			
			include(dirname(__FILE__) . "/../action/$script");			
		}
		
	}
}