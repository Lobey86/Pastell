<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");
require_once (PASTELL_PATH . "/lib/action/ActionPossible.class.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/base/Normalizer.class.php");
require_once( PASTELL_PATH . "/lib/action/DocumentAction.class.php");
require_once( PASTELL_PATH . "/lib/base/ZenMail.class.php");
require_once( PASTELL_PATH . "/lib/notification/Notification.class.php");
require_once (PASTELL_PATH . "/lib/action/DocumentActionEntite.class.php");
require_once (PASTELL_PATH . "/lib/document/DocumentType.class.php");


$recuperateur = new Recuperateur($_POST);
$id_d = $recuperateur->get('id_d');
$action = $recuperateur->get('action');
$id_e = $recuperateur->get('id_e');
$page = $recuperateur->getInt('page',0);


$document = new Document($sqlQuery);

$infoDocument = $document->getInfo($id_d);

$zenMail = new ZenMail($zLog);
$notification = new Notification($sqlQuery);
$notificationMail = new NotificationMail($notification,$zenMail,$journal);

$documentAction = new DocumentAction($sqlQuery,$journal,$id_d,$action,$id_e,$authentification->getId());
$documentType = new DocumentType(DOCUMENT_TYPE_PATH);

$theAction = $documentType->getAction($infoDocument['type']);

$actionPossible = new ActionPossible($sqlQuery,$theAction,$documentAction);

if ( ! $actionPossible->isActionPossible($id_d,$action,$id_e,$authentification->getId())) {
	$lastError->setLastError("L'action « $action »  n'est pas permise : " .$actionPossible->getLastBadRule() );
	header("Location: detail.php?id_d=$id_d&id_e=$id_e&page=$page");
	exit;
}

if ($action == Action::MODIFICATION){
	
	header("Location: edition.php?id_d=$id_d&id_e=$id_e&page=$page");
	exit;
}

$action_script = $theAction->getActionScript($action);

$action_file = dirname(__FILE__)."/../../action/$action_script";

if (! file_exists($action_file )){
		
	$lastError->setLastError("L'action « $action » est inconnue, veuillez contacter votre administrateur Pastell");
	
	header("Location: detail.php?id_d=$id_d&id_e=$id_e&page=$page");
	exit;
}
require($action_file);




