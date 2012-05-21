<?php

require_once("init-api.php");
require_once( PASTELL_PATH ."/lib/journal/Journal.class.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/api/CSVoutput.class.php");


$recuperateur = new Recuperateur($_REQUEST);
$offset = $recuperateur->getInt('offset',0);
$limit = $recuperateur->getInt('limit',100);
$id_e = $recuperateur->getInt('id_e',0);
$type = $recuperateur->get('type');
$id_d = $recuperateur->get('id_d');
$id_user = $recuperateur->get('id_user');
$recherche = $recuperateur->get('recherche');
$date_debut = $recuperateur->get('date_debut');
$date_fin = $recuperateur->get('date_fin');

$format = $recuperateur->get('format');


if   (! $roleUtilisateur->hasDroit($id_u,"journal:lecture",$id_e)){
	$JSONoutput->displayErrorAndExit("Acces interdit id_e=$id_e, id_d=$id_d,id_u=$id_u,type=$type");
}

$all = $journal->getAll($id_e,$type,$id_d,$id_user,$offset,$limit,$recherche,$date_debut,$date_fin) ;


if ($format == 'csv'){
	$CSVoutput = new CSVoutput();
	$CSVoutput->sendAttachment("pastell-export-journal-$id_e-$type-$id_d.csv",$all);

} else {
	$JSONoutput->display($all);
}
