<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");


$recuperateur = new Recuperateur($_POST);

$id_u = $recuperateur->get('id_u');
$id_e = $recuperateur->get('id_e',0);
$type = $recuperateur->get('type',0);

$notification = new Notification($sqlQuery);


if ($roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$id_e)
	|| ($id_u==$authentification->getId() && $roleUtilisateur->hasDroit($authentification->getId(),"entite:lecture",$id_e))
) {
	$notification->add($id_u,$id_e,$type,0);
}

if ($id_u == $authentification->getId()){
	header("Location: moi.php");
	exit;
}
header("Location: detail.php?id_u=$id_u");