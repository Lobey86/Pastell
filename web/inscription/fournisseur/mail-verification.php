<?php 
require_once( dirname(__FILE__) . "/../../init.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/utilisateur/Utilisateur.class.php");
require_once( PASTELL_PATH . "/lib/utilisateur/UtilisateurListe.class.php");

$recuperateur = new Recuperateur($_GET);

$password = $recuperateur->get('password');
$login = $recuperateur->get('login');

$utilisateurListe = new UtilisateurListe($sqlQuery);
$id_u = $utilisateurListe->getUtilisateurByLogin($login);

$utilisateur = new Utilisateur($sqlQuery,$id_u);
$result = $utilisateur->validMail($password);

if ($result){
	$lastMessage->setLastMessage("Votre mail est maintenant validé");
} else {
	$lastError->setLastError("Le mail n'a pas pu être validé");
}

header("Location: " .SITE_BASE. "connexion/connexion.php");
