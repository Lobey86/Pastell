<?php 

require_once( dirname(__FILE__) . "/../init-authenticated.php");


require_once (PASTELL_PATH . "/lib/formulaire/Formulaire.class.php");
$formulaire = new Formulaire( PASTELL_PATH ."/form/inscription-fournisseur.yml");


require_once(PASTELL_PATH . "/lib/FileFournisseurInscriptionPath.class.php");
$fileFournisseurPath = new FileFournisseurInscriptionPath(FOURNISSEUR_XML_FILE);

require_once( PASTELL_PATH . "/lib/formulaire/DonneesFormulaire.class.php");
$donneesFormulaire = new DonneesFormulaire($fileFournisseurPath->getFilePath($infoEntite['siren']));
$donneesFormulaire->setFormulaire($formulaire);

