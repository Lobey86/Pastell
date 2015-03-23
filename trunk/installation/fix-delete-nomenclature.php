<?php

require_once( __DIR__ . "/../web/init.php");



$result = $objectInstancier->Document->getAllByType('actes-cdg85');


foreach($result as $document_info){
	
	$file_path = $objectInstancier->DonneesFormulaireFactory->getNewDirectoryPath($document_info['id_d'])."{$document_info['id_d']}.yml";
	
	$file_content = file_get_contents($file_path);
	
	if (preg_match("#^nomemclature: \"\"#m",$file_content)){
		echo "Document {$document_info['id_d']} potentiellement touché.\n";
	}
	
}



