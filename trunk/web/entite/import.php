<?php
require_once( dirname(__FILE__) . "/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/entite/EntiteListe.class.php");
require_once( PASTELL_PATH . "/lib/entite/EntiteListeHTML.class.php");
require_once( PASTELL_PATH . "/lib/skin/TabDecoratorHTML.class.php");
require_once( PASTELL_PATH . "/lib/skin/ImportFormHTML.class.php");

$recuperateur = new Recuperateur($_GET);
$entiteListe = new EntiteListe($sqlQuery);
$importForm = new ImportFormHTML(new EntiteListeHTML());
$tabDecoratorHTML = new TabDecoratorHTML();

$id_e = $recuperateur->getInt('id_e',0);
$page =  $recuperateur->getInt('page',0);

if ( ! $roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$id_e) ) {
	header("Location: " . SITE_BASE ."index.php");
	exit;
}

$denomination = "";
if ($id_e){
	$entite = new Entite($sqlQuery,$id_e);
	$info = $entite->getInfo();
	$denomination = $info['denomination'];
}

$page_title = "Importer";


include( PASTELL_PATH ."/include/haut.php");
include (PASTELL_PATH."/include/bloc_message.php"); 

$tabDecoratorHTML->display(array("Collectivités","Agents"),"entite/import.php?id_e=$id_e",$page);

if ($page == 0){
	$allCDG = $entiteListe->getAll(Entite::TYPE_CENTRE_DE_GESTION);
	$importForm->displayImportCol($id_e,$denomination,$allCDG);
} else {
	$importForm->displayImportAgent();
}

include( PASTELL_PATH ."/include/bas.php");

