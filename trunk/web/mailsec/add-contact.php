<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/mailsec/Annuaire.class.php");
require_once( PASTELL_PATH . "/lib/helper/mail_validator.php");

$recuperateur = new Recuperateur($_POST);
$id_e = $recuperateur->get('id_e');
$description = $recuperateur->get('description');
$email = $recuperateur->get('email');


if ( ! $roleUtilisateur->hasDroit($authentification->getId(),"annuaire:edition",$id_e)) {
	header("Location: annuaire.php?id_e=$id_e");
	exit;
}

if ( ! is_mail($email)){
	$lastError->setLastError("$email ne semble pas être un email valide");
	header("Location: annuaire.php?id_e=$id_e");
	exit;
}

$annuaire = new Annuaire($sqlQuery,$id_e);

if($annuaire->getFromEmail($email)){
	$lastError->setLastError("$email existe déjà dans l'annuaire");
	header("Location: annuaire.php?id_e=$id_e");
	exit;	
}

$annuaire->add($description,$email);

$mail = htmlentities("\"$description\"<$email>",ENT_QUOTES);

$lastMessage->setLastMessage("$mail a été ajouté à la liste de contacts");
header("Location: annuaire.php?id_e=$id_e");