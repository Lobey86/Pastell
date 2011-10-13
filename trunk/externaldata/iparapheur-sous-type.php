<?php

require_once( PASTELL_PATH . "/externaldata/lib/IParapheurType.class.php");



$page_title = "Choix d'un type de document";
include( PASTELL_PATH ."/include/haut.php");

$iParapheurType= new IParapheurType();
$iParapheurType->displaySousType($sqlQuery,$donneesFormulaireFactory,$id_d,$id_e,$page,$field,$type);

include( PASTELL_PATH ."/include/bas.php");