<?php

require_once(dirname(__FILE__)."/../init-authenticated.php");

$recuperateur = new Recuperateur($_GET);

$id_j = $recuperateur->get('id_j');

$info  = $journal->getInfo($id_j);

if  (! $roleUtilisateur->hasDroit($authentification->getId(),"journal:lecture",$info['id_e'])){
	header("Location: index.php");
	exit;
}

header("Content-Type: text/plain; charset=ISO-8859-15");
header("Content-disposition: attachment; filename=preuve.txt");
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Pragma: public");

echo $info['message'];

