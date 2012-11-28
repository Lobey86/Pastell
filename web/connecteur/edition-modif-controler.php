<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");


require_once( PASTELL_PATH . "/lib/FileUploader.class.php");

require_once (PASTELL_PATH . "/lib/formulaire/Formulaire.class.php");
require_once( PASTELL_PATH . "/lib/formulaire/DonneesFormulaire.class.php");

$recuperateur = new Recuperateur($_POST);
$id_e = $recuperateur->getInt('id_e',0);
$libelle = $recuperateur->get('libelle');
$old_libelle = $recuperateur->get('old_libelle');
$page = 0;

$entite = new Entite($sqlQuery,$id_e);
$entiteInfo = $entite->getInfo();

if ( ! $roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$id_e)) {
	header("Location: list.php");
	exit;
}

$documentType = $objectInstancier->ConnecteurFactory->getDocumentType($id_e,$libelle);
$formulaire = $documentType->getFormulaire();

$donneesFormulaire = $objectInstancier->ConnecteurFactory->getDataFormulaire($id_e,$libelle);


$fileUploader = new FileUploader($_FILES);

	

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


header("Location: " . SITE_BASE . "connecteur/edition.php?id_e=$id_e&libelle=$old_libelle");
