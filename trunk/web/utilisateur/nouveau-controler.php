<?php
include( dirname(__FILE__) . "/../init-authenticated.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");

require_once( PASTELL_PATH . "/lib/Siren.class.php");
require_once( PASTELL_PATH . "/lib/Redirection.class.php");
require_once( PASTELL_PATH . "/lib/MailVerification.class.php");
require_once( PASTELL_PATH . "/lib/utilisateur/UtilisateurCreator.class.php");
require_once( PASTELL_PATH . '/lib/notification/MailNotificationSQL.class.php');

$recuperateur = new Recuperateur($_POST);
$email = $recuperateur->get('email');
$siren = $recuperateur->get('siren');
$login = $recuperateur->get('login');
$password = $recuperateur->get('password');
$password2 = $recuperateur->get('password2');
$nom = $recuperateur->get('nom');
$prenom = $recuperateur->get('prenom');

$redirection = new Redirection("nouveau.php?siren=$siren");

$entite = new Entite($sqlQuery,$siren);

if (! $entite->exists()){
	$lastError->setLastError("Le siren que vous avez déjà indiqué est inconnu");
	$redirection->redirect();
}

$utilisateurCreator = new UtilisateurCreator($sqlQuery);
$id_u = $utilisateurCreator->create($login,$password,$password2,$email);

if ( ! $id_u){
	$lastError->setLastError($utilisateurCreator->getLastError());
	$redirection->redirect();
}

$utilisateur = new Utilisateur($sqlQuery,$id_u);
$utilisateur->validMailAuto();
$utilisateur->setNomPrenom($nom,$prenom);

$infoUtilisateur = $utilisateur->getInfo();
$mailNotificationSQL = new MailNotificationSQL($sqlQuery);
$mailNotificationSQL->addNotification($siren,$infoUtilisateur['email'],"default");

$entite->addRole($id_u,"proprietaire");

$redirection->redirect(SITE_BASE . "entite/detail.php?siren=$siren");