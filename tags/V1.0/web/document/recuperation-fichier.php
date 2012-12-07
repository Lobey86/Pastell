<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once (PASTELL_PATH . "/lib/formulaire/Formulaire.class.php");
require_once( PASTELL_PATH . "/lib/formulaire/DonneesFormulaire.class.php");
require_once( PASTELL_PATH . "/lib/transaction/message/MessageRessource.class.php");

$recuperateur = new Recuperateur($_GET);
$id_d = $recuperateur->get('id_d');
$id_e = $recuperateur->get('id_e');
$field = $recuperateur->get('field');
$num = $recuperateur->getInt('num');

$donneesFormulaire = $donneesFormulaireFactory->get($id_d,'collectivite-properties');


$file_path = $donneesFormulaire->getFilePath($field,$num);
$file_name_array = $donneesFormulaire->get($field);
$file_name= $file_name_array[$num];

if (! file_exists($file_path)){
	$lastError->setLastError("Ce fichier n'existe pas");
	header("Location: index.php");
	exit;
}

$utilisateur = new Utilisateur($sqlQuery,$authentification->getId());
$infoUtilisateur = $utilisateur->getInfo();
$nom = $infoUtilisateur['prenom']." ".$infoUtilisateur['nom'];

$journal->add(Journal::DOCUMENT_CONSULTATION,$id_e,$id_d,"Consult�","$nom a consult� le document $file_name");



header("Content-type: ".mime_content_type($file_path));
header("Content-disposition: attachment; filename=\"$file_name\"");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Pragma: public");

readfile($file_path);