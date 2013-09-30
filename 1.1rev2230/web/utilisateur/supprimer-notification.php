<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");

$recuperateur = new Recuperateur($_POST);
$id_n = $recuperateur->get('id_n');


$notification = new Notification($sqlQuery);
$infoNotification = $notification->getInfo($id_n);

$id_u = $infoNotification['id_u'];
$id_e = $infoNotification['id_e'];

if ($roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$id_e)
	|| ($id_u==$authentification->getId() && $roleUtilisateur->hasDroit($authentification->getId(),"entite:lecture",$id_e)
	))
	{
	$notification->remove($id_n);
}

if ($id_u == $authentification->getId()){
	header("Location: moi.php");
	exit;
}
header("Location: detail.php?id_u=$id_u");
