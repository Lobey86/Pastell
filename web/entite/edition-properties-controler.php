<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");

require_once( PASTELL_PATH . "/lib/FileUploader.class.php");

require_once (PASTELL_PATH . "/lib/formulaire/Formulaire.class.php");
require_once( PASTELL_PATH . "/lib/formulaire/DonneesFormulaire.class.php");
require_once (PASTELL_PATH . "/lib/action/ActionCreator.class.php");
require_once (PASTELL_PATH . "/lib/base/PKCS12.class.php");

//Récupération des données
$recuperateur = new Recuperateur($_POST);
$id_e = $recuperateur->get('id_e');
$page = $recuperateur->get('page');

$entite = new Entite($sqlQuery,$id_e);
$entiteInfo = $entite->getInfo();

if ( ! $roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$id_e)) {
	header("Location: list.php");
	exit;
}

$documentType = $documentTypeFactory->getDocumentType('collectivite-properties');
$formulaire = $documentType->getFormulaire();
$formulaire->setTabNumber($page);


$fileUploader = new FileUploader($_FILES);

	
$donneesFormulaire = $donneesFormulaireFactory->get($id_e,'collectivite-properties');

$donneesFormulaire->saveTab($recuperateur,$fileUploader,$page);

$donneesFormulaire->setData('type_id_e',$entiteInfo['type']);

$pkcs12 = new PKCS12();
$p12_data = $pkcs12->getAll($donneesFormulaire->getFilePath('tdt_user_certificat'),$donneesFormulaire->get('tdt_user_certificat_password'));

if ($p12_data){
	$donneesFormulaire->addFileFromData("tdt_user_certificat_pem","tdt_user_certificat_pem",$p12_data['cert']); 
	$donneesFormulaire->addFileFromData("tdt_user_key_pem","tdt_user_key_pem",$p12_data['pkey']); 
	$donneesFormulaire->addFileFromData("tdt_user_certificat_and_key_pem","tdt_user_certificat_and_key_pem",$p12_data['cert']."\n".$p12_data['pkey']);
}

$p12_data = $pkcs12->getAll($donneesFormulaire->getFilePath('iparapheur_user_certificat'),$donneesFormulaire->get('iparapheur_user_certificat_password'));

if ($p12_data){
	$donneesFormulaire->addFileFromData("iparapheur_user_key_pem","iparapheur_user_key_pem",$p12_data['pkey'].$p12_data['cert']); 
}




$type = $recuperateur->get('suivant');
if ($type){
	header("Location: edition-properties.php?id_e=$id_e&page=".($page+1));
	exit;
}
$type = $recuperateur->get('precedent');
if ($type){
	header("Location: edition-properties.php?id_e=$id_e&page=".($page - 1));
	exit;
}

$type = $recuperateur->get('ajouter');
if ($type){
	header("Location: edition-properties.php?id_e=$id_e&page=$page");
	exit;
}
header("Location: " . SITE_BASE . "entite/detail.php?id_e=$id_e&page=$page");
