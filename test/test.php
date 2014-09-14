<?php

require_once( __DIR__ . "/../web/init.php");


$zenMail = $objectInstancier->ZenMail;

$zenMail->setEmetteur("Pastell",PLATEFORME_MAIL);
$zenMail->setDestinataire("eric@sigmalis.com");
$zenMail->setSujet("[Pastell] Notification");
$zenMail->setContenuText("test");

$filename = "test.pdf";
$filepath = "/Users/eric/Desktop/BDC-addulact-projet-2014-9.pdf";

$zenMail->addAttachment($filename, $filepath);

$zenMail->send();