<?php 
require_once( __DIR__ . "/../web/init.php");

$handle = opendir(WORKSPACE_PATH);

if (!$handle){
	echo "Impossible d'ouvrir ".WORKSPACE_PATH."\n";
	exit;
}
while (false !== ($entry = readdir($handle))) {
	$file = WORKSPACE_PATH ."/$entry";
	if (! is_file($file)){
		continue;
	}
	if (strpos($entry, "connecteur_") === 0){
		continue;
	}
	echo "Déplacement de $entry\n";
	$a = $entry[0];
	$b = $entry[1];
	$new_path = WORKSPACE_PATH."/$a/$b/";
	if (! file_exists($new_path)) {
		mkdir($new_path,0777,true);
	}
	rename($file, $new_path."/$entry");
}

closedir($handle);
