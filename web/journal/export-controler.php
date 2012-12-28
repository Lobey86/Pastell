<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");
require_once (PASTELL_PATH . "/lib/helper/date.php");


$recuperateur = new Recuperateur($_REQUEST);
$id_e = $recuperateur->getInt('id_e',0);
$type = $recuperateur->get('type');
$id_d = $recuperateur->get('id_d');
$id_u = $recuperateur->get('id_u');
$recherche = $recuperateur->get('recherche');
$date_debut = $recuperateur->get('date_debut');
$date_fin = $recuperateur->get('date_fin');

if   (! $roleUtilisateur->hasDroit($authentification->getId(),"journal:lecture",$id_e)){
	header("Location: index.php");
	exit;
}

$date_debut = date_fr_to_iso($date_debut);
$date_fin = date_fr_to_iso($date_fin);

list($sql,$value) = $journal->getQueryAll($id_e,$type,$id_d,$id_u,0,-1,$recherche,$date_debut,$date_fin) ;

$sqlQuery->prepareAndExecute($sql,$value);
$CSVoutput = new CSVoutput();
$CSVoutput->displayHTTPHeader("pastell-export-journal-$id_e-$id_u-$type-$id_d.csv");

$CSVoutput->begin();
while($sqlQuery->hasMoreResult()){
	$data = $sqlQuery->fetch();
	unset($data['preuve']);
	$CSVoutput->displayLine($data);
}
$CSVoutput->end();
