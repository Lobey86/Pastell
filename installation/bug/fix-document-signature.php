<?php 
require_once( __DIR__ . "/../../web/init.php");

//Traite le cas des bordereaux de signature pour le flux document-cdg85

$flux_name = 'document-cdg85';
$old_field_name = 'document_signe';
$new_field_name = 'bordereau';


$result = $objectInstancier->Document->getAllByType($flux_name);

if (!$result){
	echo "Il n'y a pas de document de type $flux_name\n";
	exit;
}

echo "Les documents suivants vont etre modifies : \n";

$nb = 0;

foreach($result as $document_info){

	$file_path = $objectInstancier->DonneesFormulaireFactory->getNewDirectoryPath($document_info['id_d'])."{$document_info['id_d']}.yml";

	$old_name = $file_path."_document_signe_0";
	$new_name = $file_path."_bordereau_0";

	if (! file_exists($old_name) || file_exists($new_name)){
		continue;
	}
	echo "Rename : $old_name $new_name\n";
	$nb++;
}

echo "\n\n$nb document vont être modifié !\n";

echo "Etes-vous sur (o/N) ? ";
$fh = fopen('php://stdin', 'r');
$entree = trim(fgets($fh,1024));

if ($entree != 'o'){
	exit;
}

foreach($result as $document_info){
	
	$file_path = $objectInstancier->DonneesFormulaireFactory->getNewDirectoryPath($document_info['id_d'])."{$document_info['id_d']}.yml"; 

	$old_name = $file_path."_document_signe_0";
	$new_name = $file_path."_bordereau_0";
	
	if (! file_exists($old_name) || file_exists($new_name)){
		continue;
	}

	rename($old_name, $new_name);
	echo "Rename : $old_name $new_name\n";
}
