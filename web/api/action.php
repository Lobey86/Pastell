<?php
require_once("init-api.php");
require_once (PASTELL_PATH . "/lib/action/ActionPossible.class.php");

$recuperateur = new Recuperateur($_REQUEST);
$id_e = $recuperateur->getInt('id_e',0);
$id_d = $recuperateur->get('id_d');
$action = $recuperateur->get('action');
$id_destinataire = $recuperateur->get('destinataire');
if (!$id_destinataire){
	$id_destinataire = array();
}

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

$result = $objectInstancier->ActionExecutorFactory->executeOnDocument($id_e,$id_u,$id_d,$action,$id_destinataire);
$message = $objectInstancier->ActionExecutorFactory->getLastMessage();

if ($result){
	$JSONoutput->display(array("result" => $result,"message"=>$message));
} else {
	$JSONoutput->displayErrorAndExit($message);
}





