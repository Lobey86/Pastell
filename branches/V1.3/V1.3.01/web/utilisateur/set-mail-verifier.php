<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");


$recuperateur = new Recuperateur($_GET);

$id_u = $recuperateur->get('id_u');

$utilisateur = new Utilisateur($sqlQuery);
$utilisateur->validMailAuto($id_u);

header("Location: index.php");