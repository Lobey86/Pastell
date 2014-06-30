<?php
require_once("init-api.php");

$recuperateur = new Recuperateur($_REQUEST);
$id_u_role = $recuperateur->getInt('id_u');
$role = $recuperateur->get('role');
$id_e = $recuperateur->getInt('id_e');

$api_json->addRoleUtilisateur($id_u_role, $role, $id_e);

?>