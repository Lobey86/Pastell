<?php
require_once( dirname(__FILE__) . "/../web/init.php");

require_once(PASTELL_PATH . "/lib/action/DocumentActionList.class.php");
require_once (PASTELL_PATH . "/lib/action/DocumentAction.class.php");
require_once (PASTELL_PATH . "/lib/document/DocumentEntite.class.php");
require_once (PASTELL_PATH . "/lib/action/DocumentActionEntite.class.php");
require_once( PASTELL_PATH . "/lib/base/ZenMail.class.php");
require_once( PASTELL_PATH . "/lib/notification/Notification.class.php");



$documentActionList = new DocumentActionList($sqlQuery);
$list = $documentActionList->getFromAction('send-tdt');

$documentEntite = new DocumentEntite($sqlQuery);

$journal = new Journal($sqlQuery,0);

$zenMail = new ZenMail($zLog);
$notification = new Notification($sqlQuery);
$notificationMail = new NotificationMail($notification,$zenMail,$journal);

foreach ($list as $document){
	
	$id_d = $document['id_d'];
	$allEntite = $documentEntite->getEntite($id_d);
	
	$id_e = $allEntite[0]['id_e'];
	
	
	$documentAction = new DocumentAction($sqlQuery,$journal,$id_d,$id_e,0);
	$id_a = $documentAction->addAction('acquiter-tdt');
	
	$documentActionEntite = new DocumentActionEntite($sqlQuery);
	$documentActionEntite->addAction($id_a,$id_e,$journal);
	$message = "Le document $id_d a été acquitté par le contrôle de légalité";
	$notificationMail->notify($id_e,$id_d,'acquiter-tdt', 'rh-actes',$message);
	
	
	
}
