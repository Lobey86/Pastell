<?php

require_once( PASTELL_PATH . "/controler/ChoixTypeParapheurControler.class.php");


$choixTypeParapheurControler = new ChoixTypeParapheurControler($sqlQuery,$donneesFormulaireFactory);

$result = $choixTypeParapheurControler->set($id_e,$id_d,$type,$recuperateur);

if ( ! $result){
	$lastError->setLastError($choixTypeParapheurControler->getLastError());
} 

header("Location: edition.php?id_d=$id_d&id_e=$id_e&page=$page");
