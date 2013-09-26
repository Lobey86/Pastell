<?php
require_once(dirname(__FILE__)."/../init.php");

$recuperateur = new Recuperateur($_POST);

$login = $recuperateur->get('login');
$password = $recuperateur->get('password');

$authentificationConnecteur = $objectInstancier->ConnecteurFactory->getGlobalConnecteur("authentification");

if ($authentificationConnecteur && $login != 'admin'){
	$lastError->setLastError("Veuillez utiliser le serveur CAS pour l'authentification");
	header("Location: connexion.php");
	exit;
}



$utilisateurListe = new UtilisateurListe($sqlQuery);
$id_u = $utilisateurListe->getUtilisateurByLogin($login);

$utilisateur = new Utilisateur($sqlQuery);

if ( ! $utilisateur->verifPassword($id_u,$password) ){
	$lastError->setLastError("Login ou mot de passe incorrect.");
	header("Location: connexion.php");
	exit;
}


$certificatConnexion = new CertificatConnexion($sqlQuery);

if (! $certificatConnexion->connexionGranted($id_u)){
	$lastError->setLastError("Vous devez avoir un certificat valide pour ce compte");
	header("Location: connexion.php");
	exit;
}


$infoUtilisateur = $utilisateur->getInfo($id_u);
if (! $infoUtilisateur['mail_verifie']) {
	$_SESSION['id_u'] = $id_u;
	header("Location: " . SITE_BASE . "inscription/fournisseur/inscription-mail-en-cours.php?id_u=$id_u");
	exit;
}

$journal->setId($id_u);
$nom = $infoUtilisateur['prenom']." ".$infoUtilisateur['nom'];
$journal->add(Journal::CONNEXION,$infoUtilisateur['id_e'],0,"Connecté","$nom s'est connecté depuis l'adresse ".$_SERVER['REMOTE_ADDR']);

$authentification->connexion($login, $id_u);
header("Location: " . SITE_BASE . "index.php");