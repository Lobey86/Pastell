<?php
require_once("../init.php");

$certificatConnexion = new CertificatConnexion($sqlQuery);
$id_u = $certificatConnexion->autoConnect();
	
if ( ! $id_u ) {
	header("connexion.php");
	exit;
}

$utilisateur = new Utilisateur($sqlQuery);
$utilisateurInfo = $utilisateur->getInfo($id_u);

$journal->setId($id_u);
$nom = $utilisateurInfo['prenom']." ".$utilisateurInfo['nom'];
$journal->add(Journal::CONNEXION,$utilisateurInfo['id_e'],0,"Connecté","$nom s'est connecté automatiquement depuis l'adresse ".$_SERVER['REMOTE_ADDR']);


$authentification->connexion($utilisateurInfo['login'],$id_u);

header("Location: " . SITE_BASE);