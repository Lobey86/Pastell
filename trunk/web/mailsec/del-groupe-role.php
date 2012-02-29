<?php 
require_once(dirname(__FILE__)."/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/mailsec/AnnuaireRoleSQL.class.php");

$recuperateur = new Recuperateur($_POST);
$all_id_r = $recuperateur->get('id_r',array());
$id_e = $recuperateur->get('id_e');

$annuaireRoleSQL = new AnnuaireRoleSQL($sqlQuery);



foreach($all_id_r as $id_r) {
	$info = $annuaireRoleSQL->getInfo($id_r);

	if ( $roleUtilisateur->hasDroit($authentification->getId(),"annuaire:edition",$info['id_e_owner'])) {
		$annuaireRoleSQL->delete($id_r);
		$lastMessage->setLastMessage("Les groupes sélectionnés ont été supprimés");
	}
}



header("Location: groupe-role-list.php?id_e=$id_e");