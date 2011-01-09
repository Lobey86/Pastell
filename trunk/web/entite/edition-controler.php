<?php
require_once( dirname(__FILE__) . "/../init-authenticated.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/Redirection.class.php");
require_once( PASTELL_PATH . "/lib/Siren.class.php");
require_once( PASTELL_PATH . "/lib/entite/EntiteCreator.class.php");
require_once( PASTELL_PATH . "/lib/entite/EntiteModifier.class.php");
require_once( PASTELL_PATH . "/lib/entite/EntiteProperties.class.php");

$recuperateur = new Recuperateur($_POST);

$id_e = $recuperateur->get('id_e');
$nom = $recuperateur->get('nom');
$siren = $recuperateur->get('siren',0);
$type = $recuperateur->get('type');
$entite_mere =  $recuperateur->get('entite_mere',0);
$centre_de_gestion =  $recuperateur->get('centre_de_gestion',0);
$has_ged = $recuperateur->get('has_ged',0);
$has_archivage = $recuperateur->get('has_archivage',0);

if ( (! $roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$id_e))
	&& (! $roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$entite_mere))

){
	header("Location: " . SITE_BASE ."index.php");
	exit;
}

$redirection = new Redirection("edition.php?type=$type&id_e=$id_e&entite_mere=$entite_mere");

if($id_e){
	$entite = new Entite($sqlQuery,$id_e);
}



if ($type != Entite::TYPE_SERVICE) {
	
	if ( ! $siren ){
		$lastError->setLastError("Le siren est obligatoire");
		$redirection->redirect();
	} 
	$sirenVerifier = new Siren();
	
	if (  ! ($sirenVerifier->isValid($siren) || ($id_e && $entite->exists()))){
		$lastError->setLastError("Votre siren ne semble pas valide");
		$redirection->redirect();
	}
} 




if ($type == Entite::TYPE_SERVICE && ! $entite_mere){
	$lastError->setLastError("Un service doit être ataché à une entité mère (collectivité, centre de gestion ou service)");
	$redirection->redirect();
}


if (!$nom){
	$lastError->setLastError("Le nom est obligatoire");
	$redirection->redirect();
}

if ($id_e && $entite->exists()){
	$entiteModifier = new EntiteModifier($sqlQuery,$journal,$id_e);
	$entiteModifier->update($siren,$nom,$type,$entite_mere);
} else {
	$entiteCreator = new EntiteCreator($sqlQuery,$journal);
	$id_e = $entiteCreator->create($siren,$nom,$type,$entite_mere);
	$entiteModifier = new EntiteModifier($sqlQuery,$journal,$id_e);
}

if ($centre_de_gestion){
	$entiteModifier->setCentreDeGestion($centre_de_gestion);
}

$entiteProperties = new EntiteProperties($sqlQuery,$id_e);

$entiteProperties->setProperties(EntiteProperties::ALL_FLUX,'has_ged',$has_ged);
$entiteProperties->setProperties(EntiteProperties::ALL_FLUX,'has_archivage',$has_archivage);

$lastError->deleteLastInput();

header("Location: detail.php?id_e=$id_e");



