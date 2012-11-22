<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");


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

list($sql,$value) = $journal->getQueryAll($id_e,$type,$id_d,$id_u,$offset,$limit,$recherche) ;

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
