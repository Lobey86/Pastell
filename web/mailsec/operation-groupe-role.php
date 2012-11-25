<?php 
require_once(dirname(__FILE__)."/../init-authenticated.php");

$recuperateur = new Recuperateur($_POST);
$all_id_r = $recuperateur->get('id_r',array());
$id_e = $recuperateur->getInt('id_e');
$submit = $recuperateur->get('submit');

$annuaireRoleSQL = $objectInstancier->AnnuaireRoleSQL;

foreach($all_id_r as $id_r) {
	$info = $annuaireRoleSQL->getInfo($id_r);

	if ( $roleUtilisateur->hasDroit($authentification->getId(),"annuaire:edition",$info['id_e_owner'])) {
		if ($submit == "Supprimer"){
			$annuaireRoleSQL->delete($id_r);
			$lastMessage->setLastMessage("Les groupes sélectionnés ont été supprimés");
		} elseif($submit == "Partager"){
			$annuaireRoleSQL->partage($id_r);
			$lastMessage->setLastMessage("Les groupes sélectionnés sont accessibles aux entités filles");
		} else {
			$annuaireRoleSQL->unpartage($id_r);
			$lastMessage->setLastMessage("Les groupes sélectionnés ne sont plus accessibles aux entités filles");
		}		
	}
}



header("Location: groupe-role-list.php?id_e=$id_e");