<?php 
require_once(dirname(__FILE__)."/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/mailsec/AnnuaireGroupe.class.php");

$recuperateur = new Recuperateur($_POST);
$id_e = $recuperateur->getInt('id_e');
$id_g = $recuperateur->get('id_g');
$id_a = $recuperateur->get('id_a');

if ( ! $roleUtilisateur->hasDroit($authentification->getId(),"annuaire:edition",$id_e)) {
	header("Location: annuaire.php?id_e=$id_e");
	exit;
}

$annuaireGroupe = new AnnuaireGroupe($sqlQuery,$id_e);
$annuaireGroupe->deleteFromGroupe($id_g,$id_a);

$lastMessage->setLastMessage("Email retiré du groupe");
header("Location: groupe.php?id_e=$id_e&id_g=$id_g");