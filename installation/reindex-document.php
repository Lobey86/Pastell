<?php
require_once( __DIR__ . "/../web/init.php");

if (count($argv) < 3){
	echo "{$argv[0]} : Permet de réindexer un ensemble de document d'un certain type pour un champ donnée\n";
	echo "Usage : {$argv[0]} type_de_document champ_a_reindexer [offset] [limit]\n";
	exit;
}

$document_type = get_argv(1);
$champs_reindex = get_argv(2);

$offset = get_argv(3)?:0;
$limit = get_argv(4)?:-1;



$objectInstancier->DocumentControler->reindex($document_type,$champs_reindex,$offset,$limit);
