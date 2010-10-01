<?php
include( dirname(__FILE__) . "/../init-authenticated.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");

require_once( PASTELL_PATH . "/lib/Siren.class.php");
require_once( PASTELL_PATH . "/lib/Redirection.class.php");
require_once( PASTELL_PATH . "/lib/MailVerification.class.php");
require_once( PASTELL_PATH . "/lib/utilisateur/UtilisateurCreator.class.php");
require_once( PASTELL_PATH . '/lib/notification/Notification.class.php');

$recuperateur = new Recuperateur($_POST);
$email = $recuperateur->get('email');
$id_e = $recuperateur->get('id_e');
$login = $recuperateur->get('login');
$password = $recuperateur->get('password');
$password2 = $recuperateur->get('password2');
$nom = $recuperateur->get('nom');
$prenom = $recuperateur->get('prenom');
$role = $recuperateur->get('role');

$redirection = new Redirection("edition.php?id_e=$id_e");

$entite = new Entite($sqlQuery,$id_e);

if (! $entite->exists()){
	$lastError->setLastError("L'entité est est inconnu");
	$redirection->redirect();
}

$utilisateurCreator = new UtilisateurCreator($sqlQuery,$journal);
$id_u = $utilisateurCreator->create($login,$password,$password2,$email);

if ( ! $id_u){
	$lastError->setLastError($utilisateurCreator->getLastError());
	$redirection->redirect();
}

$utilisateur = new Utilisateur($sqlQuery,$id_u);
$utilisateur->validMailAuto();
$utilisateur->setNomPrenom($nom,$prenom);

$infoUtilisateur = $utilisateur->getInfo();
/*$notification = new Notification($sqlQuery);
$notification->addNotification($id_e,$infoUtilisateur['email'],"default");
*/
$roleUtilisateur->addRole($id_u,$role,$id_e);


$redirection->redirect(SITE_BASE . "entite/detail.php?id_e=$id_e");