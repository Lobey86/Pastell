<?php
require_once("init-api.php");

$recuperateur = new Recuperateur($_REQUEST);
$id_u_a_lire = $recuperateur->get('id_u');

$api_json->detailUtilisateur($id_u_a_lire);

?>
