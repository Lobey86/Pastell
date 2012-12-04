<?php

require_once( PASTELL_PATH . "/externaldata/lib/IParapheurType.class.php");
$iParapheurType= new IParapheurType();

$page_title = "Choix d'un type de document";
include( PASTELL_PATH ."/include/haut.php");


$type_iparapheur = $formulaire->getField('iparapheur_type')->getProperties('default');

if (! $type_iparapheur){
	$lastError->setLastError("Le type iparapheur n'est pas connu pour ce flux ($type)");
	header("Location: edition.php?id_d=$id_d&id_e=$id_e&page=$page");
	exit;
}

$iParapheurType->setType($donneesFormulaireFactory,$id_d,$type,$type_iparapheur);
$iParapheurType->displaySousType($sqlQuery,$donneesFormulaireFactory,$id_d,$id_e,$page,$field,$type);

include( PASTELL_PATH ."/include/bas.php");