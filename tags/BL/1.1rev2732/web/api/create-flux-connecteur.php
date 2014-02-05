<?php

require_once("init-api.php");
$recuperateur = new Recuperateur($_REQUEST);
// Récupération des paramètres de la requête.    
$id_e = $recuperateur->getInt('id_e');
$id_ce = $recuperateur->getInt('id_ce');
$flux = $recuperateur->get('flux');
$type = $recuperateur->get('type');

$api_json->createFluxConnecteur($id_e, $flux, $type, $id_ce);

?>