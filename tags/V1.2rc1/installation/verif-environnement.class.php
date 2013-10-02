<?php
require_once(__DIR__."/../lib/VerifEnvironnement.class.php");

$verif = new VerifEnvironnement();
$php = $verif->checkPHP();

echo "Version nécessaire : {$php['min_value']}\n";
echo "Version trouvée : {$php['environnement_value']}\n";

$extension = $verif->checkExtension();
foreach($extension as $name => $present){
	echo "$name : ".($present?"ok":"CETTE EXTENSION EST MANQUANTE")."\n";
}

include(__DIR__."/../DefaultSettings.php");

if (! $verif->checkWorkspace()){
	echo "PROBLEME SUR LE WORSKSPACE_PATH : " . $verif->getLastError()."\n";
}