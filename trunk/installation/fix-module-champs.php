<?php 
require_once( __DIR__ . "/../web/init.php");

$flux_name = get_argv(1);
$old_field_name = get_argv(2);
$new_field_name = get_argv(3);

if (count($argv) != 4){	
	echo "{$argv[0]} : Modifie le nom d'un champs d'un flux dans tous les fichiers issus des instances de ce flux\n";
	echo "Usage : {$argv[0]} nom_du_module ancien_nom_du_champ nouveau_nom_du_champ\n";
	exit;
}


$result = $objectInstancier->Document->getAllByType($flux_name);

if (!$result){
	echo "Il n'y a pas de document de type $flux_name\n";
	exit;
}

echo "Les documents suivants vont etre modifies : \n";
foreach($result as $line){
	echo "{$line['id_d']} : {$line['titre']} \n"; 	
}
$nb = count($result);
echo "\n\n$nb document vont être modifié !\n";

echo "Etes-vous sur (o/N) ? ";
$fh = fopen('php://stdin', 'r');
$entree = trim(fgets($fh,1024));

if ($entree != 'o'){
	exit;
}

foreach($result as $document_info){
	
	$file_path = $objectInstancier->DonneesFormulaireFactory->getNewDirectoryPath($document_info['id_d'])."{$document_info['id_d']}.yml"; 
	$file_content = file_get_contents($file_path);
	$file_content = preg_replace("#^$old_field_name:#m", "$new_field_name:", $file_content);

	file_put_contents($file_path, $file_content);
	echo $document_info['id_d']. " : OK \n";
	
	
}

//$objectInstancier->DocumentControler->fixModuleChamps($flux_name,$old_field_name,$new_field_name);
echo "Les documents ont ete modifies\n";
	