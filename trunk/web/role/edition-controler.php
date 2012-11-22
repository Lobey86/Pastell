<?php
include( dirname(__FILE__) . "/../init-authenticated.php");

$droitChecker->verifDroitOrRedirect("role:edition",0);

$recuperateur = new Recuperateur($_POST);
$role = $recuperateur->get('role');
$libelle = $recuperateur->get('libelle');

$roleSQL = new RoleSQL($sqlQuery);

$roleSQL->edit($role,$libelle);

header("Location: detail.php?role=$role");