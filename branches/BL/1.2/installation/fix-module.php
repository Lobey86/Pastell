<?php 
require_once( __DIR__ . "/../web/init.php");

$old_flux_name = get_argv(1);
$new_flux_name = get_argv(2);

if (count($argv) != 3){	
	echo "{$argv[0]} : Modifie le nom d'un module dans la base de données\n";
	echo "Usage : {$argv[0]} ancien_nom_du_module nouveau_nom_du_module\n";
	exit;
}


$result = $objectInstancier->Document->getAllByType($old_flux_name);

if (!$result){
	echo "Il n'y a pas de document de type $old_flux_name\n";
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

$objectInstancier->Document->fixModule($old_flux_name,$new_flux_name);
echo "Les documents ont ete modifies\n";
	