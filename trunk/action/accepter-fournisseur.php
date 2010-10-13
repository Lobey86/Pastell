<?php

require_once( PASTELL_PATH . "/lib/entite/EntiteRelation.class.php");

$documentEntite = new DocumentEntite($sqlQuery);
$id_fournisseur = $documentEntite->getEntiteWithRole($id_d,"editeur");

$entiteRelation = new EntiteRelation($sqlQuery);
$entiteRelation->addRelation($id_fournisseur,EntiteRelation::IS_FOURNISSEUR,$id_e);


$entite = new Entite($sqlQuery,$id_fournisseur);
$infoEntite = $entite->getInfo();
$nomFournisseur = $infoEntite['denomination'];

$entite = new Entite($sqlQuery,$id_e);
$infoEntite = $entite->getInfo();
$nomCol = $infoEntite['denomination'];


$actionCreator = new ActionCreator($sqlQuery,$journal,$id_d);
$actionCreator->addAction($id_e,$authentification->getId(),$action,"L'inscription de $nomFournisseur a été accepté");
$actionCreator->addToEntite($id_fournisseur,"$nomCol a accepté l'inscription");


$lastMessage->setLastMessage("Le fournisseur a été accepté.");
	
header("Location: detail.php?id_d=$id_d&id_e=$id_e");