<?php
require_once( dirname(__FILE__) . "/../init-authenticated.php");

$recuperateur = new Recuperateur($_POST);
$id_e = $recuperateur->getInt('id_e');
$libelle = $recuperateur->get('libelle');
$id_connecteur = $recuperateur->get('id_connecteur');


if ( ! $objectInstancier->ConnecteurFactory->addConnecteur($id_e,$libelle,$id_connecteur)) {
	$lastError->setLastError($objectInstancier->ConnecteurFactory->getLastError());
} else {
	$lastMessage->setLastMessage("Connecteur ajouté avec succès");
}
header("Location: ".SITE_BASE."/entite/detail.php?id_e=$id_e&page=2");