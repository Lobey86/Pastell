<?php
require_once(dirname(__FILE__)."/../init.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/utilisateur/Utilisateur.class.php");
require_once( PASTELL_PATH . "/lib/entite/Entite.class.php");
require_once( PASTELL_PATH . "/lib/utilisateur/UtilisateurListe.class.php");
require_once( PASTELL_PATH . "/lib/authentification/CertificatConnexion.class.php");
require_once( PASTELL_PATH . "/lib/base/PasswordGenerator.class.php");

$recuperateur = new Recuperateur($_POST);

$mail_verif_password = $recuperateur->get('mail_verif_password');
$password = $recuperateur->get('password');
$password2 = $recuperateur->get('password2');


$utilisateurListe = new UtilisateurListe($sqlQuery);
$id_u = $utilisateurListe->getByVerifPassword($mail_verif_password);

if ( ! $id_u ){
	$lastError->setLastError("Utilisateur inconnu");
	header("Location: connexion.php");
	exit;
}

if (! $password){
	$lastError->setLastError("Le mot de passe est obligatoire");
	header("Location: changement-mdp.php?mail_verif=$mail_verif_password");
	exit;
}
if ($password != $password2){
	$lastError->setLastError("Les mots de passes ne correspondent pas");
	header("Location: changement-mdp.php?mail_verif=$mail_verif_password");
	exit;
}


$utilisateur = new Utilisateur($sqlQuery, $id_u);
$infoUtilisateur = $utilisateur->getInfo();
$utilisateur->setPassword($password);

$passwordGenerator = new PasswordGenerator();
$mailVerifPassword = $passwordGenerator->getPassword();
$utilisateur->reinitPassword($mailVerifPassword);

$journal->add(Journal::MODIFICATION_UTILISATEUR,$infoUtilisateur['id_e'],0,"mot de passe modifié","{$infoUtilisateur['login']} ({$infoUtilisateur['id_u']}) a modifié son mot de passe");
$lastMessage->setLastMessage("Votre mot de passe a été modifié");

header("Location: connexion.php");