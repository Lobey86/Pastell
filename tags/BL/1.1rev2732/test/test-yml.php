<?php 
require_once( __DIR__ . "/../web/init.php");

if (empty($argv[1])){
	echo "Usage : {$argv[0]} fichier.yml\n";
	exit;
}

$file_content = file_get_contents($argv[1]);

$result = spyc_load($file_content);
print_r($result);
