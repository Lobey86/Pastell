<?php

require_once("init-api.php");
$recuperateur = new Recuperateur($_REQUEST);
// Récupération des paramètres de la requête.    
$id_e = $recuperateur->getInt('id_e');
$id_connecteur = $recuperateur->get('id_connecteur');
$libelle = $recuperateur->get('libelle');

$api_json->createConnecteurEntite($id_e, $id_connecteur, $libelle);

?>