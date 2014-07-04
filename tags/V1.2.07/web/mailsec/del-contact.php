<?php 
require_once(dirname(__FILE__)."/../init-authenticated.php");

$recuperateur = new Recuperateur($_POST);
$id_e = $recuperateur->getInt('id_e');
$email = $recuperateur->get('email');

if ( ! $roleUtilisateur->hasDroit($authentification->getId(),"annuaire:edition",$id_e)) {
	header("Location: annuaire.php?id_e=$id_e");
	exit;
}

$annuaire = new Annuaire($sqlQuery,$id_e);
$annuaireGroupe = new AnnuaireGroupe($sqlQuery, $id_e);
foreach ($email as $mail){
	$id_a = $annuaire->getFromEmail($mail);
	if ($annuaireGroupe->hasAGroupe($id_a)){
		$lastError->setLastError("Impossible de supprimer $mail : celui-ci appartient à un groupe");
		header("Location: annuaire.php?id_e=$id_e");
		exit;
	}
	
}
$annuaire->delete($email);

$lastMessage->setLastMessage("Email supprimé de la liste de contacts");
header("Location: annuaire.php?id_e=$id_e");