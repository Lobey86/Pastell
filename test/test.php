<?php 
require_once( __DIR__ . "/../web/init.php");

$passwordGenerator = new PasswordGenerator();
$tmp_dir = $passwordGenerator->getPassword();
$zip = new ZipArchive();
$zip->open("/tmp/MP29102012-1.zip");
$zip->extractTo("/tmp/$tmp_dir/");

$phar = new PharData("/tmp/$tmp_dir.zip");
$phar->buildFromDirectory("/tmp/$tmp_dir/MP29102012-1");

rrmdir("/tmp/$tmp_dir");

echo "$tmp_dir.zip\n";

function rrmdir($dir) {	
	if (! is_dir($dir)) {
		return;
	}
	foreach ( scandir($dir) as $object) {
		if (in_array($object,array(".",".."))) {
			continue;
		}
		if (is_dir("$dir/$object")){
			rrmdir("$dir/$object");
		} else {
			unlink("$dir/$object");
		}
	}
	rmdir($dir);
}
