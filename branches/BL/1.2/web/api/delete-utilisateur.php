<?php

require_once("init-api.php");

$recuperateur = new Recuperateur($_REQUEST);
$id_u_a_supprimer = $recuperateur->get('id_u');

$api_json->deleteUtilisateur($id_u_a_supprimer);

?>
