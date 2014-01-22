<?php 
require_once( __DIR__ . "/../web/init.php");

$flux = get_argv(1);
$action_from = get_argv(2);
$action_to = get_argv(3);

if (count($argv) != 4){	
	echo "{$argv[0]} : Modifie le nom d'une action dans la base de données\n";
	echo "Usage : {$argv[0]} flux action_from action_to\n";
	exit;
}
	
$result = $objectInstancier->DocumentEntite->getAllByFluxAction($flux,$action_from);

if (!$result){
	echo "Aucun document $flux n'est dans l'état $action_from\n";
	exit;
}

echo "Les documents suivants vont etre modifies : \n";
foreach($result as $line){
	echo "{$line['denomination']} : {$line['id_d']}\n"; 	
}
echo "Etes-vous sur (o/N) ? ";
$fh = fopen('php://stdin', 'r');
$entree = trim(fgets($fh,1024));

if ($entree != 'o'){
	exit;
}

$objectInstancier->DocumentEntite->fixAction($flux,$action_from,$action_to);
echo "Les documents ont ete modifies\n";
