<?php
require_once("init-api.php");

ini_set("soap.wsdl_cache_enabled", 0);

$server = new SoapServer("http://192.168.1.5/adullact/pastell/web/api/wsdl.php");
$server->setClass("APIExecutor",$apiAction);
$server->handle();
