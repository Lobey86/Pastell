<?php

require_once("init-api.php");
$recuperateur = new Recuperateur($_REQUEST);
// Récupération des paramètres de la requête.    
$id_e = $recuperateur->getInt('id_e');
$id_ce = $recuperateur->get('id_ce');

$api_json->deleteConnecteurEntite($id_e, $id_ce);

?>