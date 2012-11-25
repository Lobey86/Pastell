<?php
require_once("init-api.php");

$recuperateur = new Recuperateur($_REQUEST);
$id_e = $recuperateur->getInt('id_e',0);
$id_d = $recuperateur->get('id_d');
$action = $recuperateur->get('action');
$id_destinataire = $recuperateur->get('destinataire');
if (!$id_destinataire){
	$id_destinataire = array();
}

$document = $objectInstancier->Document;
$info = $document->getInfo($id_d);
if (!$info || ! $roleUtilisateur->hasDroit($id_u,"{$info['type']}:edition",$id_e)){
	$JSONoutput->displayErrorAndExit("Acces interdit id_e=$id_e, id_d=$id_d,id_u=$id_u");
}

$actionPossible = $objectInstancier->ActionPossible;

if ( ! $actionPossible->isActionPossible($id_e,$id_u,$id_d,$action)) {
	$JSONoutput->displayErrorAndExit("L'action « $action »  n'est pas permise : " .$actionPossible->getLastBadRule());
}

$result = $objectInstancier->ActionExecutorFactory->executeOnDocument($id_e,$id_u,$id_d,$action,$id_destinataire);
$message = $objectInstancier->ActionExecutorFactory->getLastMessage();

if ($result){
	$JSONoutput->display(array("result" => $result,"message"=>$message));
} else {
	$JSONoutput->displayErrorAndExit($message);
}





