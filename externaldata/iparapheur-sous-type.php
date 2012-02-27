<?php

require_once( PASTELL_PATH . "/externaldata/lib/IParapheurType.class.php");
$iParapheurType= new IParapheurType();

if (! $iParapheurType->isEnabled($sqlQuery,$id_e,$donneesFormulaireFactory)){
	$lastError->setLastError("Le module iparapheur n'est pas activé");
	header("Location: edition.php?id_d=$id_d&id_e=$id_e&page=$page");
	exit;
}
$page_title = "Choix d'un type de document";
include( PASTELL_PATH ."/include/haut.php");




$iParapheurType->displaySousType($sqlQuery,$donneesFormulaireFactory,$id_d,$id_e,$page,$field,$type);

include( PASTELL_PATH ."/include/bas.php");