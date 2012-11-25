<?php

/***
 * 
 * Permet de reconstruire la table des ancetres de collectivités
 * 
 */

require_once( __DIR__ . "/../web/init.php");

$sql = "DELETE FROM entite_ancetre";
$sqlQuery->query($sql);

$sql = "INSERT INTO entite_ancetre(id_e_ancetre,id_e,niveau) VALUES (0,0,0)";
$sqlQuery->query($sql);

$sql = "SELECT entite_mere,id_e FROM entite";
$allEntite = $sqlQuery->query($sql);
$entiteCreator = new EntiteCreator($sqlQuery,$journal);

foreach($allEntite as $entite){
	$entiteCreator->updateAncetre($entite['id_e'],$entite['entite_mere']);
}
