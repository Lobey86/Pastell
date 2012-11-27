<?php
//Crée un admin (crée aussi le role admin et fixe les droits si il n'existe pas

require_once( dirname(__FILE__) . "/../web/init.php");

function getArgv($nb){
	global $argv;
	if (isset($argv[$nb])){
		return $argv[$nb];
	}
	return false;
}

$login = getArgv(1);
$password = getArgv(2);
$email = getArgv(3);

$result = $objectInstancier->AdminControler->createAdmin($login,$password,$email);

if ($result){
		echo "Administrateur $login crée avec succès\n";
	
} else {
	echo $objectInstancier->AdminControler->getLastError()."\n";	
	echo "Usage : {$argv[0]} login password email\n";
}
