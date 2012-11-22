<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");
$recuperateur = new Recuperateur($_POST);

$oldpassword = $recuperateur->get('old_password');
$password = $recuperateur->get('password');
$password2 = $recuperateur->get('password2');

if ($password != $password2){
	$lastError->setLastError("Les mots de passe ne correspondent pas");
	header("Location: modif-password.php");
	exit;
}

$utilisateur = new Utilisateur($sqlQuery,$authentification->getId());

if ( ! $utilisateur->verifPassword($oldpassword)){
	$lastError->setLastError("Votre ancien mot de passe est incorrecte");
	header("Location: modif-password.php");
	exit;
}


$utilisateur->setPassword($password);


$lastMessage->setLastMessage("Votre mot de passe a été modifié");
header("Location: moi.php");