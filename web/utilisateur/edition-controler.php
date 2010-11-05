<?php
include( dirname(__FILE__) . "/../init-authenticated.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");

require_once( PASTELL_PATH . "/lib/Siren.class.php");
require_once( PASTELL_PATH . "/lib/Redirection.class.php");
require_once( PASTELL_PATH . "/lib/MailVerification.class.php");
require_once( PASTELL_PATH . "/lib/utilisateur/UtilisateurCreator.class.php");
require_once( PASTELL_PATH . '/lib/notification/Notification.class.php');
require_once( PASTELL_PATH . "/lib/base/Certificat.class.php");

$recuperateur = new Recuperateur($_POST);
$email = $recuperateur->get('email');
$id_e = $recuperateur->get('id_e');
$id_u = $recuperateur->get('id_u');

$login = $recuperateur->get('login');
$password = $recuperateur->get('password');
$password2 = $recuperateur->get('password2');
$nom = $recuperateur->get('nom');
$prenom = $recuperateur->get('prenom');
$role = $recuperateur->get('role');

$redirection = new Redirection("edition.php?id_e=$id_e&id_u=$id_u");

//$entite = new Entite($sqlQuery,$id_e);

/*if (! $entite->exists() && ! $id_u){
	$lastError->setLastError("L'entité est est inconnu");
	$redirection->redirect();
}*/

if (! $id_u){
	$utilisateurCreator = new UtilisateurCreator($sqlQuery,$journal);
	$id_u = $utilisateurCreator->create($login,$password,$password2,$email);
	
	if ( ! $id_u){
		$lastError->setLastError($utilisateurCreator->getLastError());
		$redirection->redirect();
	}
}
$utilisateur = new Utilisateur($sqlQuery,$id_u);



if (isset($_FILES['certificat']) && $_FILES['certificat']['tmp_name']){
	
	$certificat_pem = file_get_contents($_FILES['certificat']['tmp_name']);
	$certificat = new Certificat($certificat_pem);
	
	if ( ! $utilisateur->setCertificat($certificat)){
		$lastError->setLastError("Le certificat n'est pas valide");
		$redirection->redirect();
	} 
}

if ( $password && $password2 ){
	if ($password != $password2){
		$lastError->setLastError("Les mot de passes ne correspondent pas");
		$redirection->redirect();
	}
	$utilisateur->setPassword($password);
}
$utilisateur->validMailAuto();
$utilisateur->setNomPrenom($nom,$prenom);
$utilisateur->setEmail($email);
$utilisateur->setLogin($login);
$utilisateur->setColBase($id_e);



$redirection->redirect(SITE_BASE . "utilisateur/detail.php?id_u=$id_u");