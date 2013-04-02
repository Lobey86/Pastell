<?php

require_once(dirname(__FILE__)."/../init-authenticated.php");

$recuperateur = new Recuperateur($_GET);

$id_j = $recuperateur->get('id_j');

$info  = $journal->getInfo($id_j);

if  (! $roleUtilisateur->hasDroit($authentification->getId(),"journal:lecture",$info['id_e'])){
	header("Location: index.php");
	exit;
}

header("Content-Type: application/timestamp-reply");
header("Content-Transfer-Encoding: base64");
header("Content-disposition: attachment; filename=preuve.tsr");

header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Pragma: public");

echo base64_encode($info['preuve']);