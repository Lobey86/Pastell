<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");
require_once (PASTELL_PATH . "/lib/action/ActionPossible.class.php");
require_once( PASTELL_PATH . "/lib/notification/Notification.class.php");
require_once( PASTELL_PATH . "/lib/notification/NotificationMail.class.php");
require_once (PASTELL_PATH . "/lib/action/ActionCreator.class.php");
require_once (PASTELL_PATH . "/lib/action/ActionPossibleFactory.class.php");


$recuperateur = new Recuperateur($_POST);
$id_d = $recuperateur->get('id_d');
$action = $recuperateur->get('action');
$id_e = $recuperateur->get('id_e');
$page = $recuperateur->getInt('page',0);
$go = $recuperateur->getInt('go',0);


$document = new Document($sqlQuery);

$infoDocument = $document->getInfo($id_d);

$type = $infoDocument['type'];

$zenMail = new ZenMail($zLog);
$notification = new Notification($sqlQuery);
$notificationMail = new NotificationMail($notification,$zenMail,$journal);

$documentType = $documentTypeFactory->getDocumentType($type);
$theAction = $documentType->getAction();
$formulaire = $documentType->getFormulaire();

$actionName = $theAction->getActionName($action);

$donneesFormulaire = $donneesFormulaireFactory->get($id_d,$type);

$entite = new Entite($sqlQuery,$id_e);


$id_e_col = $entite->getCollectiviteAncetre();
$collectiviteProperties = $donneesFormulaireFactory->get($id_e_col,'collectivite-properties');


$actionPossible = new ActionPossible($sqlQuery,$id_e,$authentification->getId(),$theAction);
$actionPossible->setRoleUtilisateur($roleUtilisateur);
$actionPossible->setDonnesFormulaire($donneesFormulaire);
$actionPossible->setEntite($entite);
$actionPossible->setHeritedProperties($collectiviteProperties);

if ( ! $actionPossible->isActionPossible($id_d,$action)) {
	$lastError->setLastError("L'action « $action »  n'est pas permise : " .$actionPossible->getLastBadRule() );
	header("Location: detail.php?id_d=$id_d&id_e=$id_e&page=$page");
	exit;
}

if ($action == Action::MODIFICATION){
	header("Location: edition.php?id_d=$id_d&id_e=$id_e&page=$page");
	exit;
}


$id_destinataire = array();

$action_destinataire =  $theAction->getActionDestinataire($action);
if ($action_destinataire) {
	$id_destinataire = $recuperateur->get('destinataire');
	
	if (! $id_destinataire){
		header("Location: " . SITE_BASE . "/entite/choix-entite.php?id_d=$id_d&id_e=$id_e&action=$action&type=$action_destinataire");
		exit;
	}

}

if ($theAction->getWarning($action) && ! $go){
	header("Location: warning.php?id_d=$id_d&id_e=$id_e&action=$action&page=$page");
	exit;
}

$action_class_name = $theAction->getActionClass($action);

$action_class_file = dirname(__FILE__)."/../../action/$action_class_name.class.php";
if (! file_exists($action_class_file )){
	$lastError->setLastError("L'action « $action » est inconnue, veuillez contacter votre administrateur Pastell");	
	header("Location: detail.php?id_d=$id_d&id_e=$id_e&page=$page");
	exit;
}




require_once($action_class_file);

$actionCreator = new ActionCreator($sqlQuery,$journal,$id_d);

$actionClass = new $action_class_name($zLog,$sqlQuery,$id_d,$id_e,$authentification->getId(),$type);
$actionClass->setNotificationMail($notificationMail);
$actionClass->setAction($action);
$actionClass->setDestinataire($id_destinataire);
$actionClass->setCollectiviteProperties($collectiviteProperties);

$result = $actionClass->go();

if ($result){
	$lastMessage->setLastMessage($actionClass->getLastMessage());
} else {
	$lastError->setLastError($actionClass->getLastMessage());
}

header("Location: detail.php?id_d=$id_d&id_e=$id_e&page=$page");




