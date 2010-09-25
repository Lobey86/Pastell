<?php
include( dirname(__FILE__) . "/../../init.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/base/ZenMail.class.php");
require_once( PASTELL_PATH . "/lib/base/PasswordGenerator.class.php");

require_once( PASTELL_PATH . "/lib/Siren.class.php");
require_once( PASTELL_PATH . "/lib/Redirection.class.php");
require_once( PASTELL_PATH . "/lib/MailVerification.class.php");
require_once( PASTELL_PATH . "/lib/utilisateur/Utilisateur.class.php");
require_once( PASTELL_PATH . "/lib/utilisateur/UtilisateurCreator.class.php");
require_once( PASTELL_PATH . "/lib/entite/Entite.class.php");


$redirection = new Redirection("index.php");
$recuperateur = new Recuperateur($_POST);

$email = $recuperateur->get('email');
$siren = $recuperateur->get('siren');
$login = $recuperateur->get('login');
$password = $recuperateur->get('password');
$password2 = $recuperateur->get('password2');
$nom = $recuperateur->get('nom');
$prenom = $recuperateur->get('prenom');
$denomination = $recuperateur->get('denomination');


$entite = new Entite($sqlQuery,$siren);
if ($entite->exists()){
	$lastError->setLastError("Le siren que vous avez déjà indiqué est déjà connu sur la plateforme");
	$redirection->redirect();
}

$sirenVerifier = new Siren();
if (! $sirenVerifier->isValid($siren)){
	$lastError->setLastError("Votre siren ne semble pas valide");
	$redirection->redirect();
}

if ( ! $denomination ){
	$lastError->setLastError("Il faut saisir une raison sociale");
	$redirection->redirect();
}

$utilisateurCreator = new UtilisateurCreator($sqlQuery);
$id_u = $utilisateurCreator->create($login,$password,$password2,$email);

if ( ! $id_u){
	$lastError->setLastError($utilisateurCreator->getLastError());
	$redirection->redirect();
}

$utilisateur = new Utilisateur($sqlQuery,$id_u);
$utilisateur->setNomPrenom($nom,$prenom);
//TODO : ca ne doit pas fonctionner ca ...
$entite->save($denomination,"fournisseur",null);
$roleUtilisateur->addRole($id_u,"admin",$siren);

$infoUtilisateur = $utilisateur->getInfo();

$zMail = new ZenMail($zLog);
$mailVerification = new MailVerification($zMail);
$mailVerification->send($infoUtilisateur);

$redirection->redirect("inscription-ok.php");