<?php 
require_once( PASTELL_PATH. "/lib/document/DocumentType.class.php");
require_once( PASTELL_PATH. "/lib/skin/PageDecorator.class.php");
require_once( PASTELL_PATH. "/lib/skin/LienRetourHTML.class.php");

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
	$pageDecorator->addToMainMenu("Accueil", SITE_BASE . "document/index.php","picto_flux");
	$pageDecorator->addToMainMenu("Administration", SITE_BASE . "entite/detail.php","picto_utilisateurs");
	$pageDecorator->addToMainMenu("Journal des évènements", SITE_BASE . "journal/index.php","picto_journal");
	if ($roleUtilisateur->hasDroit($authentification->getId(),"role:lecture",0)){
		$pageDecorator->addToMainMenu("Rôles", SITE_BASE . "role/index.php","picto_collectivites");
	}
	
	$pageDecorator->addToMainMenu("Aide", AIDE_URL ,"picto_aide");
	
	if  ($roleUtilisateur->hasDroit($authentification->getId() ,'test:lecture',0)) {
		$pageDecorator->addToMainMenu("Environnement système", SITE_BASE . "system/index.php","picto_collectivites");
	}
	
	$entiteBC = new Entite($sqlQuery,$id_e_menu);
	foreach( $entiteBC->getAncetre() as $infoEntiteBR){
		$pageDecorator->addToBreadCrumbs($infoEntiteBR['denomination']);
	}
	
	$allType = array();

	$allDocType = $objectInstancier->DocumentTypeFactory->getAllType();
	$allDroit = $roleUtilisateur->getAllDroit($authentification->getId());

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
}
$infoVersionning = $versionning->getAllInfo();
$pageDecorator->setVersion($infoVersionning['version-complete']);


$pageDecorator->displayHaut();

$lienRetourHTML = new LienRetourHTML();

