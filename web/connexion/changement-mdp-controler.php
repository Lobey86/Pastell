<?php
require_once(dirname(__FILE__)."/../init.php");


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


$utilisateur = new Utilisateur($sqlQuery);
$infoUtilisateur = $utilisateur->getInfo($id_u);
$utilisateur->setPassword(id_u,$password);

$passwordGenerator = new PasswordGenerator();
$mailVerifPassword = $passwordGenerator->getPassword();
$utilisateur->reinitPassword($id_u,$mailVerifPassword);

$journal->add(Journal::MODIFICATION_UTILISATEUR,$infoUtilisateur['id_e'],0,"mot de passe modifié","{$infoUtilisateur['login']} ({$infoUtilisateur['id_u']}) a modifié son mot de passe");
$lastMessage->setLastMessage("Votre mot de passe a été modifié");

header("Location: connexion.php");