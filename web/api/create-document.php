<?php
require_once("init-api.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/document/Document.class.php");
require_once( PASTELL_PATH . "/lib/document/DocumentEntite.class.php");
require_once (PASTELL_PATH . "/lib/action/ActionCreator.class.php");

$recuperateur = new Recuperateur($_REQUEST);
$id_e = $recuperateur->getInt('id_e',0);
$type = $recuperateur->get('type');

if ( ! $roleUtilisateur->hasDroit($id_u,"$type:edition",$id_e)) {
	$JSONoutput->displayErrorAndExit("Acces interdit id_e=$id_e, type=$type,id_u=$id_u");
}



$document = new Document($sqlQuery);
$id_d = $document->getNewId();	
$document->save($id_d,$type);

$documentEntite = new DocumentEntite($sqlQuery);
$documentEntite->addRole($id_d,$id_e,"editeur");

$actionCreator = new ActionCreator($sqlQuery,$journal,$id_d);
$actionCreator->addAction($id_e,$id_u,Action::CREATION,"Création du document [webservice]");

$info['id_d'] = $id_d;

$JSONoutput->display($info);