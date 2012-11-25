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

$utilisateur = new Utilisateur($sqlQuery);

if ( ! $utilisateur->verifPassword($authentification->getId(),$oldpassword)){
	$lastError->setLastError("Votre ancien mot de passe est incorrecte");
	header("Location: modif-password.php");
	exit;
}


$utilisateur->setPassword($id_u,$password);


$lastMessage->setLastMessage("Votre mot de passe a été modifié");
header("Location: moi.php");