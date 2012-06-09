<?php 

require_once("../../init.php");
require_once( PASTELL_PATH . "/lib/base/ZenMail.class.php");
require_once( PASTELL_PATH . "/lib/MailVerification.class.php");
require_once( PASTELL_PATH . "/lib/utilisateur/Utilisateur.class.php");

$utilisateur = new Utilisateur($sqlQuery,$authentification->getId());
$infoUtilisateur = $utilisateur->getInfo();

if ( ! $infoUtilisateur || $infoUtilisateur['mail_verifie']){
	header("Location: " . SITE_BASE ."index.php");
	exit;
}

$zMail = new ZenMail($zLog);
$mailVerification = new MailVerification($zMail);
$mailVerification->send($infoUtilisateur);

header("Location: inscription-ok.php");