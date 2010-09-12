<?php
require_once(dirname(__FILE__)."/../init.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/utilisateur/Utilisateur.class.php");
require_once( PASTELL_PATH . "/lib/entite/Entite.class.php");
require_once( PASTELL_PATH . "/lib/utilisateur/UtilisateurListe.class.php");

$recuperateur = new Recuperateur($_POST);

$login = $recuperateur->get('login');
$password = $recuperateur->get('password');

$utilisateurListe = new UtilisateurListe($sqlQuery);
$id_u = $utilisateurListe->getUtilisateurByLogin($login);

$utilisateur = new Utilisateur($sqlQuery, $id_u);

if ( ! $utilisateur->verifPassword($password) ){
	$lastError->setLastError("Login ou mot de passe incorrect.");
	header("Location: connexion.php");
	exit;
}

$authentification->connexion($login, $id_u);

$utilisateurEntite = new UtilisateurEntite($sqlQuery,$id_u);

$siren = $utilisateurEntite->getSiren();
$role = $utilisateurEntite->getRole();

$type = Entite::TYPE_COLLECTIVITE;
if ($siren){
	$entite = new Entite($sqlQuery,$siren);
	$info = $entite->getInfo();
	$type = $info['type'];
	$breadcrumbs = $entite->getBreadCrumbs();
	$authentification->setBreadCrumbs($breadcrumbs);
	
}

$authentification->setRole($role,$siren,$type);




header("Location: " . SITE_BASE . "index.php");