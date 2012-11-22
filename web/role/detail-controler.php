<?php
require_once( dirname(__FILE__) . "/../init-authenticated.php");

$recuperateur = new Recuperateur($_POST);
$role = $recuperateur->get('role');
$droit = $recuperateur->get('droit');

$roleSQL = new RoleSQL($sqlQuery);

$roleSQL->updateDroit($role,$droit);

header("Location: detail.php?role=$role");