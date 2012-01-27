<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");

require_once( PASTELL_PATH ."/lib/journal/Journal.class.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/api/CSVoutput.class.php");


$recuperateur = new Recuperateur($_REQUEST);
$offset = $recuperateur->getInt('offset',0);
$limit = $recuperateur->getInt('limit',100);
$id_e = $recuperateur->getInt('id_e',0);
$type = $recuperateur->get('type');
$id_d = $recuperateur->get('id_d');
$id_u = $recuperateur->get('id_u');
$recherche = $recuperateur->get('recherche');


if   (! $roleUtilisateur->hasDroit($authentification->getId(),"journal:lecture",$id_e)){
	header("Location: index.php");
	exit;
}

$all = $journal->getAll($id_e,$type,$id_d,$id_u,$offset,$limit,$recherche) ;

$CSVoutput = new CSVoutput();
$CSVoutput->sendAttachment("pastell-export-journal-$id_e-$id_u-$type-$id_d.csv",$all);
