<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");

$recuperateur = new Recuperateur($_POST);
$id_e = $recuperateur->getInt('id_e');
$id_e_owner = $recuperateur->getInt('id_e_owner');
$role = $recuperateur->get('role');


if ( ! $roleUtilisateur->hasDroit($authentification->getId(),"annuaire:edition",$id_e)) {
	header("Location: annuaire.php?id_e=$id_e");
	exit;
}

if ( ! $roleUtilisateur->hasDroit($authentification->getId(),"annuaire:edition",$id_e_owner)) {
	header("Location: annuaire.php?id_e=$id_e");
	exit;
}

$entite = new Entite($sqlQuery,$id_e);
$infoEntite = $entite->getInfo();

if ($id_e != 0){
	$nom = "$role - {$infoEntite['denomination']}";
} else {
	$nom = "$role - toutes les collectivités";
}

$annuaireRoleSQL = $objectInstancier->AnnuaireRoleSQL;

$annuaireRoleSQL->add($nom,$id_e_owner,$id_e,$role);

$lastMessage->setLastMessage("Le groupe « $nom » a été créé");
header("Location: groupe-role-list.php?id_e=$id_e_owner");