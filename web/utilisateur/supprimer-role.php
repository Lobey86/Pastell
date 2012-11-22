<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");

$recuperateur = new Recuperateur($_POST);

$id_u = $recuperateur->get('id_u');
$role = $recuperateur->get('role');
$id_e = $recuperateur->getInt('id_e',0);
if ($roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$id_e)) {
	$roleUtilisateur->removeRole($id_u,$role,$id_e);
}
header("Location: detail.php?id_u=$id_u");