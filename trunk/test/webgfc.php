<?php 

require_once( dirname(__FILE__) . "/../web/init.php");
require_once( PASTELL_PATH . "/lib/system/WebGFC.class.php");

$webGFC = new WebGFC();

//echo $webGFC->echoTest("Hello World\n");

//print_r($webGFC->getTypes(1));

print_r($webGFC->getSousTypes(1,"Demandes citoyennes"));

//$content = base64_encode("test");


//$reponse = $webGFC->createCourrier(4,"eric@sigmalis.com","test","blalalal",$content,"pastell");

//print_r($reponse);