<?php
require_once("init-api.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/document/Document.class.php");
require_once( PASTELL_PATH . "/lib/document/DocumentEntite.class.php");
require_once (PASTELL_PATH . "/lib/action/ActionCreator.class.php");
require_once (PASTELL_PATH . "/lib/action/ActionPossible.class.php");
require_once( PASTELL_PATH . "/lib/base/ZenMail.class.php");
require_once( PASTELL_PATH . "/lib/notification/Notification.class.php");
require_once( PASTELL_PATH . "/lib/notification/NotificationMail.class.php");

$recuperateur = new Recuperateur($_REQUEST);
$id_e = $recuperateur->getInt('id_e',0);
$id_d = $recuperateur->get('id_d');
$action = $recuperateur->get('action');
$id_destinataire = $recuperateur->get('destinataire');


$document = new Document($sqlQuery);
$info = $document->getInfo($id_d);
if (!$info || ! $roleUtilisateur->hasDroit($id_u,"{$info['type']}:edition",$id_e)){
	$JSONoutput->displayErrorAndExit("Acces interdit id_e=$id_e, id_d=$id_d,id_u=$id_u");
}

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

$actionPossible = new ActionPossible($sqlQuery,$id_e,$id_u,$theAction);
$actionPossible->setRoleUtilisateur($roleUtilisateur);
$actionPossible->setDonnesFormulaire($donneesFormulaire);
$actionPossible->setEntite($entite);

if ( ! $actionPossible->isActionPossible($id_d,$action)) {
	$JSONoutput->displayErrorAndExit("L'action « $action »  n'est pas permise : " .$actionPossible->getLastBadRule());
}


$action_class_name = $theAction->getActionClass($action);


$action_class_file = dirname(__FILE__)."/../../action/$action_class_name.class.php";

if (! file_exists($action_class_file )){
	$JSONoutput->displayErrorAndExit("L'action « $action » est inconnue, veuillez contacter votre administrateur Pastell");	
}


$id_e_col = $entite->getCollectiviteAncetre();
$collectiviteProperties = $donneesFormulaireFactory->get($id_e_col,'collectivite-properties');


require_once($action_class_file);

$actionCreator = new ActionCreator($sqlQuery,$journal,$id_d);

$actionClass = new $action_class_name($zLog,$sqlQuery,$id_d,$id_e,$id_u,$type);
$actionClass->setNotificationMail($notificationMail);
$actionClass->setAction($action);
if ($id_destinataire){
	$actionClass->setDestinataire($id_destinataire);
}
$actionClass->setCollectiviteProperties($collectiviteProperties);

$result = $actionClass->go();

if ($result){
	$JSONoutput->display(array("result" => $result));
} else {
	$JSONoutput->displayErrorAndExit($actionClass->getLastMessage());
}





