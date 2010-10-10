<?php
require_once( dirname(__FILE__) . "/../web/init.php");

require_once(PASTELL_PATH . "/lib/action/DocumentActionList.class.php");
require_once (PASTELL_PATH . "/lib/action/DocumentAction.class.php");
require_once (PASTELL_PATH . "/lib/document/DocumentEntite.class.php");
require_once (PASTELL_PATH . "/lib/action/DocumentActionEntite.class.php");
require_once( PASTELL_PATH . "/lib/base/ZenMail.class.php");
require_once( PASTELL_PATH . "/lib/notification/Notification.class.php");
require_once (PASTELL_PATH . "/lib/document/Document.class.php");
require_once (PASTELL_PATH . "/lib/document/DocumentType.class.php");


$documentActionList = new DocumentActionList($sqlQuery);
$list = $documentActionList->getFromAction('send-tdt');

$documentEntite = new DocumentEntite($sqlQuery);
$documentType = new DocumentType(DOCUMENT_TYPE_PATH);

$journal = new Journal($sqlQuery,0);

$zenMail = new ZenMail($zLog);
$notification = new Notification($sqlQuery);
$notificationMail = new NotificationMail($notification,$zenMail,$journal);

foreach ($list as $document){
	
	//Réceception par le TdT
	$id_d = $document['id_d'];
	
	$document = new Document($sqlQuery);
	$infoDocument = $document->getInfo($id_d);
	
	$allEntite = $documentEntite->getEntite($id_d);
	
	$id_e = $allEntite[0]['id_e'];
	
	
	$documentAction = new DocumentAction($sqlQuery,$journal,$id_d,$id_e,0);
	$id_a = $documentAction->addAction('acquiter-tdt');
	
	$documentActionEntite = new DocumentActionEntite($sqlQuery);
	$documentActionEntite->addAction($id_a,$id_e,$journal);
	$message = "Le document $id_d a été acquitté par le contrôle de légalité";
	$notificationMail->notify($id_e,$id_d,'acquiter-tdt', 'rh-actes',$message);
	
	$theAction = $documentType->getAction($infoDocument['type']);
	include( dirname(__FILE__) . "/../action/envoyer_au_cdg.php");
	
	
}
