<?php
include( dirname(__FILE__) . "/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/Siren.class.php");
require_once( PASTELL_PATH . "/lib/Redirection.class.php");
require_once( PASTELL_PATH . "/lib/MailVerification.class.php");

$recuperateur = new Recuperateur($_POST);
$email = $recuperateur->get('email');
$id_e = $recuperateur->getInt('id_e');
$id_u = $recuperateur->get('id_u');

$login = $recuperateur->get('login');
$password = $recuperateur->get('password');
$password2 = $recuperateur->get('password2');
$nom = $recuperateur->get('nom');
$prenom = $recuperateur->get('prenom');
$role = $recuperateur->get('role');

$redirection = new Redirection("edition.php?id_e=$id_e&id_u=$id_u");


if ( ! $roleUtilisateur->hasDroit($authentification->getId(),"utilisateur:edition",$id_e)) {
	header("Location: " . SITE_BASE . "index.php");
	exit;
}

if (! $nom){
	$lastError->setLastError("Le nom est obligatoire");
	$redirection->redirect();
}

if (! $prenom){
	$lastError->setLastError("Le prénom est obligatoire");
	$redirection->redirect();
}


if (! is_mail($email)){
	$lastError->setLastError("Votre adresse email ne semble pas valide");
	$redirection->redirect();
}

if ( $password && $password2 ){
	if ($password != $password2){
		$lastError->setLastError("Les mot de passes ne correspondent pas");
		$redirection->redirect();
	}
}

if (! $id_u){
	$id_u = $objectInstancier->UtilisateurCreator->create($login,$password,$password2,$email);
	
	if ( ! $id_u){
		$lastError->setLastError($objectInstancier->utilisateurCreator->getLastError());
		$redirection->redirect();
	}
}
$utilisateur = new Utilisateur($sqlQuery);
if ( $password && $password2 ){
	$utilisateur->setPassword(id_u,$password);
}
$oldInfo = $utilisateur->getInfo($id_u);

if (isset($_FILES['certificat']) && $_FILES['certificat']['tmp_name']){
	
	$certificat_pem = file_get_contents($_FILES['certificat']['tmp_name']);
	$certificat = new Certificat($certificat_pem);
	
	if ( ! $utilisateur->setCertificat($id_u,$certificat)){
		$lastError->setLastError("Le certificat n'est pas valide");
		$redirection->redirect();
	} 
}


$utilisateur->validMailAuto($id_u);
$utilisateur->setNomPrenom($id_u,$nom,$prenom);
$utilisateur->setEmail($id_u,$email);
$utilisateur->setLogin($id_u,$login);
$utilisateur->setColBase($id_u,$id_e);

$roleUtilisateur = $objectInstancier->RoleUtilisateur;

$allRole = $roleUtilisateur->getRole($id_u);
if (! $allRole ){
	$roleUtilisateur->addRole($id_u,RoleUtilisateur::AUCUN_DROIT,$id_e);
}

$utilisateur = new Utilisateur($sqlQuery);
$newInfo = $utilisateur->getInfo($id_u);

$infoToRetrieve = array('email','login','nom','prenom');
$infoChanged = array();
foreach($infoToRetrieve as $key){
	if ($oldInfo[$key] != $newInfo[$key]){
		$infoChanged[] = "$key : {$oldInfo[$key]} -> {$newInfo[$key]}";
	}
}
$infoChanged  = implode("; ",$infoChanged);


$journal->add(Journal::MODIFICATION_UTILISATEUR,$id_e,$authentification->getId(),"Edité",
				"Edition de l'utilisateur $login ($id_u) : $infoChanged");	
	
$redirection->redirect(SITE_BASE . "utilisateur/detail.php?id_u=$id_u");