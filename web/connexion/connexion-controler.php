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


header("Location: " . SITE_BASE . "index.php");