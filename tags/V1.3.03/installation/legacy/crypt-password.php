#! /usr/bin/php
<?php 
require_once( dirname(__FILE__) . "/../web/init.php");

$sql = "SELECT id_u,password,login FROM utilisateur";

foreach($objectInstancier->SQLQuery->query($sql) as $utilisateur){
	$objectInstancier->Utilisateur->setPassword($utilisateur['id_u'],$utilisateur['password']);
	echo "Mise à jour du mot de passe de {$utilisateur['login']} \n";
}
