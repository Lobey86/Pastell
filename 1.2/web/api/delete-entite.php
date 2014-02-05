<?php
require_once("init-api.php");

$recuperateur = new Recuperateur($_REQUEST);
// Récupération des paramètres de la requête. 
$id_e_a_supprimer = $recuperateur->get('id_e');

$api_json->deleteEntite($id_e_a_supprimer);

?>