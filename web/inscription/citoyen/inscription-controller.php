<?php
include( dirname(__FILE__) . "/../../init.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/base/ZenMail.class.php");
require_once( PASTELL_PATH . "/lib/base/PasswordGenerator.class.php");

require_once( PASTELL_PATH . "/lib/Redirection.class.php");
require_once( PASTELL_PATH . "/lib/MailVerification.class.php");
require_once( PASTELL_PATH . "/lib/utilisateur/Utilisateur.class.php");
require_once( PASTELL_PATH . "/lib/utilisateur/UtilisateurCreator.class.php");
require_once( PASTELL_PATH . "/lib/entite/Entite.class.php");
require_once( PASTELL_PATH . "/lib/entite/EntiteCreator.class.php");

$redirection = new Redirection("index.php");

$recuperateur = new Recuperateur($_POST);
$email = $recuperateur->get('email');
$password = $recuperateur->get('password');
$password2 = $recuperateur->get('password2');


if ( ! $email ){
	$lastError->setLastError("Il faut saisir un email");
	$redirection->redirect();
}

$entite = new Entite($sqlQuery,$email);
if ($entite->exists()){
	$lastError->setLastError("L'adresse que vous avez déjà indiqué est déjà connu sur la plateforme");
	$redirection->redirect();
}


$utilisateurCreator = new UtilisateurCreator($sqlQuery,$journal);
$id_u = $utilisateurCreator->create($email,$password,$password2,$email);

if ( ! $id_u){
	$lastError->setLastError($utilisateurCreator->getLastError());
	$redirection->redirect();
}

$utilisateur = new Utilisateur($sqlQuery,$id_u);

$entiteCreator = new EntiteCreator($sqlQuery,$journal);
$id_e = $entiteCreator->edit(false,0,$email,Entite::TYPE_CITOYEN,0,0);

$roleUtilisateur->addRole($id_u,"citoyen",$id_e);

$infoUtilisateur = $utilisateur->getInfo();
$utilisateur->validMailAuto();
/*$zMail = new ZenMail($zLog);
$mailVerification = new MailVerification($zMail);
$mailVerification->send($infoUtilisateur);
*/
$redirection->redirect("inscription-ok.php");