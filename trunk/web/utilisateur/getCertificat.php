<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/utilisateur/UtilisateurListe.class.php");
require_once( PASTELL_PATH . "/lib/helper/suivantPrecedent.php");

$recuperateur = new Recuperateur($_GET);
$verif_number = $recuperateur->get('verif_number');

$utilisateurListe = new UtilisateurListe($sqlQuery);
$liste = $utilisateurListe->getUtilisateurByCertificat($verif_number,0,1);

if (count($liste) < 1){
	header("Location: index.php");
	exit;
}


$certificat = new Certificat($liste[0]['certificat']);


header("Content-type: text/plain");
header("Content-disposition: attachment; filename=".$verif_number.".pem");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Pragma: public");

echo $certificat->getContent();