<?php

require_once( dirname(__FILE__) . "/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/mailsec/Annuaire.class.php");
require_once( PASTELL_PATH . "/lib/mailsec/AnnuaireGroupe.class.php");

$recuperateur = new Recuperateur($_GET);
$id_e = $recuperateur->getInt('id_e');
$q = $recuperateur->get('q');
$mailOnly = $recuperateur->get('mail-only');

if ( ! $roleUtilisateur->hasDroit($authentification->getId(),"annuaire:lecture",$id_e)){
	header("Location: ".SITE_BASE."index.php");
	exit;
}

$annuaireGroupe = new AnnuaireGroupe($sqlQuery,$id_e);
$annuaire = new Annuaire($sqlQuery,$id_e);

header("Content-type: text/plain; charset=ISO-8859-1");

if (! $mailOnly){
	foreach($annuaireGroupe->getListGroupe($q) as $item){
		echo "groupe: \"".$item['nom'] . "\"\n";
	}
}

foreach ($annuaire->getListeMail($q) as $item){
	echo  '"' .$item['description'] .'"'. " <".$item['email'].">\n";
}
