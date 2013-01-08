<?php
include( dirname(__FILE__) . "/../init-authenticated.php");

$droitChecker->verifDroitOrRedirect("role:edition",0);

$recuperateur = new Recuperateur($_POST);
$role = $recuperateur->get('role');

if ($objectInstancier->RoleUtilisateur->anybodyHasRole($role)){
	$lastError->setLastError("Le role $role est attribué à des utilisateurs");
	header("Location: detail.php?role=$role");
	exit;
}

$roleSQL = new RoleSQL($sqlQuery);
$roleSQL->delete($role);

$lastMessage->setLastMessage("Le role $role a été supprimé");

header("Location: index.php");