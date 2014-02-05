<?php

require_once("init-api.php");
$recuperateur = new Recuperateur($_REQUEST);
// Récupération des paramètres de la requête.    
$id_e = $recuperateur->getInt('id_e');

$api_json->listConnecteurEntite($id_e);

?>