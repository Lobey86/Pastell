<?php 
require_once( PASTELL_PATH ."/lib/flux/FluxFactory.class.php");
require_once( PASTELL_PATH. "/lib/entite/Entite.class.php");
require_once( PASTELL_PATH. "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH. "/lib/document/DocumentType.class.php");
require_once( PASTELL_PATH. "/lib/skin/PageDecorator.class.php");
require_once( PASTELL_PATH. "/lib/helper/version.php");

header("Content-type: text/html");

$recuperateur = new Recuperateur($_GET);
$id_e_menu = $recuperateur->getInt('id_e',0);
$type_e_menu = $recuperateur->get('type',"");


if ( empty($title) ) {
	$title = "Pastell";
}

$pageDecorator = new PageDecorator();
$pageDecorator->setTitle($page_title);
$pageDecorator->setSiteBase(SITE_BASE);

if ($authentification->isConnected()){
	$pageDecorator->setUtilisateur($authentification->getId(),$authentification->getLogin());
	$pageDecorator->addToMainMenu("Documents", SITE_BASE . "document/index.php","picto_flux");
	$pageDecorator->addToMainMenu("Administration", SITE_BASE . "entite/index.php","picto_utilisateurs");
	$pageDecorator->addToMainMenu("Journal des évènements", SITE_BASE . "journal/index.php","picto_journal");
	if ($roleUtilisateur->hasOneDroit($authentification->getId() ,"fournisseur:lecture'")) {
		$pageDecorator->addToMainMenu("Fournisseurs", SITE_BASE . "entite/fournisseur.php","picto_fournisseurs");
	}
	$pageDecorator->addToMainMenu("Aide", SITE_BASE . "aide/index.php","picto_aide");
	if  ($roleUtilisateur->hasDroit($authentification->getId() ,'test:lecture',0)) {
		$pageDecorator->addToMainMenu("Tests", SITE_BASE . "test/index.php","picto_collectivites");
	}
	
	$entiteBC = new Entite($sqlQuery,$id_e_menu);
	foreach( $entiteBC->getAncetre() as $infoEntiteBR){
		$pageDecorator->addToBreadCrumbs($infoEntiteBR['denomination']);
	}
	
	$allType = array();

	$allDocType = $documentTypeFactory->getAllType();
	$allDroit = $roleUtilisateur->getDroit($authentification->getId());

	foreach($allDocType as $type_flux => $les_flux){
		foreach($les_flux as $nom => $affichage) {
			if ($roleUtilisateur->hasOneDroit($authentification->getId(),$nom.":lecture")){
				$allType[$type_flux][$nom]  = $affichage;
			}
		}
	}
	$pageDecorator->setMenuGauche($allType,$id_e_menu,$type_e_menu);
	
	if (isset($nouveau_bouton_url)){
		$pageDecorator->addNouveauBouton($nouveau_bouton_url);
	}

	$pageDecorator->setVersion(get_version());
}


$pageDecorator->displayHaut();
?>

