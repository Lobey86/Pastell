<?php
require_once("init-api.php");

$recuperateur = new Recuperateur($_REQUEST);
// Récupération des paramètres de la requête. 
$id_e_a_lire = $recuperateur->get('id_e');

$api_json->detailEntite($id_e_a_lire);


?>
