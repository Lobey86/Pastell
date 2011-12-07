<?php


require_once( PASTELL_PATH . "/controler/ChoixTypeActesControler.class.php");


$choixTypeActesControler = new ChoixTypeActesControler($sqlQuery,$donneesFormulaireFactory);

$result = $choixTypeActesControler->set($id_e,$id_d,$type,$recuperateur);

if ( ! $result){
	$lastError->setLastError($choixTypeActesControler->getLastError());
} 

header("Location: edition.php?id_d=$id_d&id_e=$id_e&page=$page");
