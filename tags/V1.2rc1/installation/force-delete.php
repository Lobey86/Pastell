#! /usr/bin/php
<?php 
require_once( __DIR__ . "/../web/init.php");

$id_d = get_argv(1);

if (!$id_d){
	echo "Usage : {$argv[0]} id_d\n";
	exit;
}

$info = $objectInstancier->Document->getInfo($id_d);
$objectInstancier->DonneesFormulaireFactory->get($id_d)->delete();
$objectInstancier->Document->delete($id_d);

$message = "Le document « {$info['titre']} » ($id_d) à été supprimé par un administrateur";
$objectInstancier->Journal->add(Journal::DOCUMENT_ACTION,0,$id_d,"suppression",$message);

echo "Le document $id_d a été supprimé\n";