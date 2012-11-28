<?php
require_once( dirname(__FILE__) . "/../init-authenticated.php");

$recuperateur = new Recuperateur($_POST);
$id_e = $recuperateur->getInt('id_e');
$flux = $recuperateur->get('flux');
$type = $recuperateur->get('type');
$id_connecteur = $recuperateur->get('id_connecteur');


if ( ! $objectInstancier->ConnecteurFactory->addConnecteur2Flux($id_e,$flux,$type,$id_connecteur)) {
	$lastError->setLastError($objectInstancier->ConnecteurFactory->getLastError());
} else {
	$lastMessage->setLastMessage("Connecteur associé au flux avec succès");
}
header("Location: ".SITE_BASE."/entite/detail.php?id_e=$id_e&page=3");