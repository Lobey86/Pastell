<?php
include( dirname(__FILE__) . "/../init-authenticated.php");

$recuperateur = new Recuperateur($_GET);
$id_u = $recuperateur->get('id_u');


$utilisateur  = new Utilisateur($sqlQuery);
$utilisateur->removeCertificat($id_u);

header("Location: edition.php?id_u=$id_u");