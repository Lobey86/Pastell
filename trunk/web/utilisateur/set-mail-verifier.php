<?php
require_once( dirname(__FILE__) . "/../init-admin.php");

require_once( ZEN_PATH . "/lib/Recuperateur.class.php");

$recuperateur = new Recuperateur($_GET);

$id_u = $recuperateur->get('id_u');

$utilisateur = new Utilisateur($sqlQuery,$id_u);
$utilisateur->validMailAuto();

header("Location: index.php");