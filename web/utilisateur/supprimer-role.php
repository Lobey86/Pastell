<?php

require_once(dirname(__FILE__)."/../init-admin.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");


$recuperateur = new Recuperateur($_POST);

$id_u = $recuperateur->get('id_u');
$role = $recuperateur->get('role');
$id_e = $recuperateur->getInt('id_e',0);

$roleUtilisateur->removeRole($id_u,$role,$id_e);

header("Location: detail.php?id_u=$id_u");