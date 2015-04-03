<?php

require_once(__DIR__."/../../web/init.php");


echo "début";
for($i=0; $i<1000; $i++){
	echo ".";
	$pass = $objectInstancier->PasswordGenerator->getPassword();
	$objectInstancier->Journal->add(Journal::CONNEXION,1,'','test','ceci est un message de test' . $pass);
}
echo "\n";
