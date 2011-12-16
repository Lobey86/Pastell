<?php 
require_once(dirname(__FILE__)."/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/mailsec/AnnuaireRoleSQL.class.php");

$recuperateur = new Recuperateur($_POST);
$id_r = $recuperateur->get('id_r');


$annuaireRoleSQL = new AnnuaireRoleSQL($sqlQuery);
$info = $annuaireRoleSQL->getInfo($id_r);


if ( ! $roleUtilisateur->hasDroit($authentification->getId(),"annuaire:edition",$info['id_e_owner'])) {
	header("Location: annuaire.php?id_e={$info['id_e_owner']}");
	exit;
}

$annuaireRoleSQL->delete($id_r);

$lastMessage->setLastMessage("Les groupes sélectionnés ont été supprimés");
header("Location: groupe-role-list.php?id_e={$info['id_e_owner']}");