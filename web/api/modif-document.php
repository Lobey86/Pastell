<?php
require_once("init-api.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/document/Document.class.php");
require_once( PASTELL_PATH . "/lib/document/DocumentEntite.class.php");
require_once (PASTELL_PATH . "/lib/action/ActionCreator.class.php");

$recuperateur = new Recuperateur($_REQUEST);
$id_e = $recuperateur->getInt('id_e',0);
$id_d = $recuperateur->get('id_d');
$type = $recuperateur->get('type');


$document = new Document($sqlQuery);
$info = $document->getInfo($id_d);
if (!$info || ! $roleUtilisateur->hasDroit($id_u,"{$info['type']}:edition",$id_e)){
	$JSONoutput->displayErrorAndExit("Acces interdit id_e=$id_e, id_d=$id_d,id_u=$id_u");
}

$donneesFormulaire = $donneesFormulaireFactory->get($id_d,$info['type']);

$fileUploader = new FileUploader($_FILES);
$modif = $donneesFormulaire->saveAll($recuperateur,$fileUploader);


$documentType = $documentTypeFactory->getDocumentType($info['type']);
$formulaire = $documentType->getFormulaire();

$titre_field = $formulaire->getTitreField();
$titre = $donneesFormulaire->get($titre_field);

$document->setTitre($id_d,$titre);

foreach($modif as $field_name){
	$field = $formulaire->getField($field_name);
	$script = $field->getProperties('controler');
	
	$name = "{$script}Controler";
	require_once(PASTELL_PATH."/controler/{$script}Controler.class.php");

	$controler = new $name($sqlQuery,$donneesFormulaireFactory);
	$controler->set($id_e,$id_d,$info['type'],$recuperateur);
}


$actionCreator = new ActionCreator($sqlQuery,$journal,$id_d);
$actionCreator->addAction($id_e,$id_u,Action::MODIFICATION,"Modification du document [WS]");

$result['result'] = "ok";
$JSONoutput->display($result);