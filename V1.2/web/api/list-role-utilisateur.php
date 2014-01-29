<?php

require_once("init-api.php");

$recuperateur = new Recuperateur($_REQUEST);
$id_u_role = $recuperateur->get('id_u');
$id_e_role = $recuperateur->get('id_e');

$api_json->listRoleUtilisateur($id_e_role, $id_u_role);

?>