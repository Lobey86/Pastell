<?php
require_once("init-api.php");

$recuperateur = new Recuperateur($_REQUEST);
$id_e = $recuperateur->getInt('id_e'); // Pour la vérification des droits

$api_json->listUtilisateur($id_e);

?>

