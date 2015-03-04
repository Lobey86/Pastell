<?php

ini_set("soap.wsdl_cache_enabled", 0);

$client = new SoapClient("http://192.168.1.5/adullact/pastell/web/api/wsdl.php",
     	array('login' => 'admin',
        'password' => 'admin', 'trace'=>1) 

);


print_r($client->action(12,"ENAs13O","verif-iparapheur"));

$response = $client->__getLastResponse();
$xml = simplexml_load_string($response);

$dom = new DOMDocument('1.0');
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom->loadXML($xml->asXML());
echo $dom->saveXML();
