<?php
require_once("init-api.php");

$recuperateur = new Recuperateur($_REQUEST);
$type = $recuperateur->get('type');
$id_e = $recuperateur->get('id_e');
$offset = $recuperateur->getInt('offset',0);
$limit = $recuperateur->getInt('limit',100);

if  (! $roleUtilisateur->hasDroit($id_u,"$type:lecture",$id_e)){
	$JSONoutput->displayErrorAndExit("Acces interdit id_e=$id_e, type=$type,id_u=$id_u");
}

$documentActionEntite = new DocumentActionEntite($sqlQuery);

$listDocument = $documentActionEntite->getListDocument($id_e , $type , $offset, $limit) ;
$JSONoutput->display($listDocument);