<?php

require_once("init-api.php");
$recuperateur = new Recuperateur($_REQUEST);
// Récupération des paramètres de la requête.    
$id_e = $recuperateur->getInt('id_e');
$id_ce = $recuperateur->getInt('id_ce');
$libelle = $recuperateur->get('libelle');

$api_json->modifConnecteurEntite($id_e, $id_ce, $libelle);

?>