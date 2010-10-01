<?php

require_once("init.php");

require_once( PASTELL_PATH . "/lib/utilisateur/Utilisateur.class.php");
require_once( PASTELL_PATH . "/lib/entite/Entite.class.php");
require_once( PASTELL_PATH . "/lib/journal/Journal.class.php");


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

$journal = new Journal($sqlQuery,$authentification->getId());