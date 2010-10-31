<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");

require_once( PASTELL_PATH . "/lib/FileUploader.class.php");

require_once (PASTELL_PATH . "/lib/formulaire/Formulaire.class.php");
require_once( PASTELL_PATH . "/lib/formulaire/DonneesFormulaire.class.php");
require_once (PASTELL_PATH . "/lib/action/ActionCreator.class.php");

//Récupération des données
$recuperateur = new Recuperateur($_POST);
$id_e = $recuperateur->get('id_e');
$page = $recuperateur->get('page');


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

$pkcs12_file = $donneesFormulaire->getFilePath('tdt_user_certificat');


$pkcs12 = file_get_contents( $pkcs12_file );

$result = openssl_pkcs12_read( $pkcs12, $certs, $donneesFormulaire->get('tdt_user_certificat_password') );
if (! $result){
	$lastError->setLastError("Impossible de lire le certificat p12");
	header("Location: edition-properties.php?id_e=$id_e&page=$page");
	exit;
}

openssl_pkey_export($certs['pkey'],$pkey,$donneesFormulaire->get('tdt_user_certificat_password'));

$donneesFormulaire->addFileFromData("tdt_user_certificat_pem","tdt_user_certificat_pem",$certs['cert']); 
$donneesFormulaire->addFileFromData("tdt_user_key_pem","tdt_user_key_pem",$pkey); 


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
header("Location: " . SITE_BASE . "entite/detail.php?id_e=$id_e&page=$page");
