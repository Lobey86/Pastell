<?php

require_once("init.php");

require_once( PASTELL_PATH . "/lib/utilisateur/Utilisateur.class.php");
require_once( PASTELL_PATH . "/lib/entite/Entite.class.php");


if (! $authentification->isConnected()){
	header("Location: " . SITE_BASE ."connexion/connexion.php");
	exit;
}

$utilisateur = new Utilisateur($sqlQuery,$authentification->getId());
$infoUtilisateur = $utilisateur->getInfo();
if (! $infoUtilisateur['mail_verifie']) {
	header("Location: " . SITE_BASE . "inscription/fournisseur/inscription-mail-en-cours.php");
	exit;
}

$entite = false;
$infoEntite = false;

if ( ! $authentification->isAdmin()){
	$utilisateurEntite = new UtilisateurEntite($sqlQuery,$authentification->getId());
	$siren = $utilisateurEntite->getSiren();
	$entite = new Entite($sqlQuery,$siren);
	$infoEntite = $entite->getInfo();
}
