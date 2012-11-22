<?php 

require_once("../../init.php");

require_once( PASTELL_PATH . "/lib/entite/Entite.class.php");
require_once( PASTELL_PATH . "/lib/utilisateur/Utilisateur.class.php");
require_once( PASTELL_PATH . "/lib/utilisateur/UtilisateurEntite.class.php");

$utilisateurEntite = new UtilisateurEntite($sqlQuery,$authentification->getId());

$entite = new Entite($sqlQuery, $utilisateurEntite->getSiren());
$result = $entite->desinscription();

if($result){
	$utilisateur = new Utilisateur($sqlQuery,$authentification->getId());
	$utilisateur->desinscription();
	
}
header("Location: index.php");
