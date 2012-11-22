<?php
require_once(dirname(__FILE__)."/../init.php");

require_once( PASTELL_PATH . "/lib/authentification/CertificatConnexion.class.php");
require_once( PASTELL_PATH . "/lib/utilisateur/UtilisateurListe.class.php");
require_once( PASTELL_PATH . "/lib/api/JSONoutput.class.php");

$JSONoutput = new JSONoutput();


$certificatConnexion = new CertificatConnexion($sqlQuery);
$id_u = $certificatConnexion->autoConnect();

if ( ! $id_u && ! empty($_SERVER['PHP_AUTH_USER'])){
	$utilisateurListe = new UtilisateurListe($sqlQuery);
	$id_u = $utilisateurListe->getUtilisateurByLogin($_SERVER['PHP_AUTH_USER']);
	$utilisateur = new Utilisateur($sqlQuery, $id_u);

	if ( ! $utilisateur->verifPassword($_SERVER['PHP_AUTH_PW']) ){
		$id_u = false;
	}
}

if (! $id_u){
	header('HTTP/1.1 401 Unauthorized');
	header('WWW-Authenticate: Basic realm="API Pastell"');
	$JSONoutput->displayErrorAndExit("Acces interdit");
}

