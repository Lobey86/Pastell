<?php



require_once( dirname(__FILE__) . "/../init-authenticated.php");

$recuperateur = new Recuperateur($_REQUEST);
$id_e = $recuperateur->getInt('id_e');
$q = $recuperateur->get('term');
$mailOnly = $recuperateur->get('mail-only');


if ( ! $roleUtilisateur->hasDroit($authentification->getId(),"annuaire:lecture",$id_e)){
	header("Location: ".SITE_BASE."index.php");
	exit;
}

$annuaireGroupe = new AnnuaireGroupe($sqlQuery,$id_e);
$annuaire = new Annuaire($sqlQuery,$id_e);
$annuaireRole = $objectInstancier->AnnuaireRoleSQL;


$result = array();

$entite = new Entite($sqlQuery,$id_e);
$infoEntite = $entite->getInfo();


$all_ancetre = $entite->getAncetreId();

$groupe_herited = $annuaireGroupe->getGroupeHerite($all_ancetre,$q);
$role_herited = $annuaireRole->getGroupeHerite($all_ancetre,$q);

if ($mailOnly == "false"){
	
	foreach($annuaireGroupe->getListGroupe($q) as $item){
		$result[] = "groupe: \"".$item['nom'] ."\"\n";
	}
	foreach($annuaireRole->getList($id_e,$q) as $item){
		$result[] = "role: \"".$item['nom'] ."\"\n";
	}
	foreach($groupe_herited as $item){
		$result[] = $annuaireGroupe->getChaineHerited($item)."\n"; 
	}
	foreach($role_herited as $item){
		$result[] = $annuaireRole->getChaineHerited($item)."\n"; 
	}
	
}


foreach ($annuaire->getListeMail($q) as $item){
	$result[] = '"'.$item['description'] . '"'." <".$item['email'].">";
}



echo json_encode($result);
