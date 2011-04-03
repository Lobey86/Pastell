<?php

require_once("init-api.php");
require_once( PASTELL_PATH ."/lib/journal/Journal.class.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");


$recuperateur = new Recuperateur($_REQUEST);
$offset = $recuperateur->getInt('offset',0);
$limit = $recuperateur->getInt('limit',100);
$id_e = $recuperateur->getInt('id_e',0);
$type = $recuperateur->get('type');
$id_d = $recuperateur->get('id_d');
$format = $recuperateur->get('format');


if   (! $roleUtilisateur->hasDroit($id_u,"journal:lecture",$id_e)){
	$JSONoutput->displayErrorAndExit("Acces interdit id_e=$id_e, id_d=$id_d,id_u=$id_u,type=$type");
}

$all = $journal->getAll($id_e,$type,$id_d,$offset,$limit) ;
if ($format == 'csv'){
	$outstream = fopen("php://output", 'w');

	foreach($all as $line){
			unset($line['preuve']);
		fputcsv($outstream,$line);
	}
	fclose($outstream);
} else {
	$JSONoutput->display($all);
}
