<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/mailsec/Annuaire.class.php");
require_once( PASTELL_PATH . "/lib/mailsec/AnnuaireGroupe.class.php");

$recuperateur = new Recuperateur($_POST);
$id_e = $recuperateur->getInt('id_e');
$name = $recuperateur->get('name');
$id_g = $recuperateur->get('id_g');

if ( ! $roleUtilisateur->hasDroit($authentification->getId(),"annuaire:edition",$id_e)) {
	header("Location: annuaire.php?id_e=$id_e");
	exit;
}

preg_match("/<([^>]*)>/",$name,$matches);
$email = $matches[1];

$annuaire = new Annuaire($sqlQuery,$id_e);
$id_a = $annuaire->getFromEmail($email);

if (! $id_a){
	$lastError->setLastError("L'email $email est inconnu");
	header("Location: groupe.php?id_e=$id_e&id_g=$id_g");
	exit;
}

$annuaireGroupe = new AnnuaireGroupe($sqlQuery,$id_e);
$annuaireGroupe->addToGroupe($id_g,$id_a);

$mail = htmlentities($name,ENT_QUOTES);

$lastMessage->setLastMessage("$mail a été ajouté à ce groupe");
header("Location: groupe.php?id_e=$id_e&id_g=$id_g");