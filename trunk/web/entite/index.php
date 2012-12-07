<?php 

require_once( dirname(__FILE__) . "/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/helper/suivantPrecedent.php");

$recuperateur = new Recuperateur($_GET);

$offset = $recuperateur->getInt('offset',0);
if ($offset <0){
	$offset = 0;
}
$search = $recuperateur->get('search');


$liste_collectivite = $roleUtilisateur->getEntiteWithDenomination($authentification->getId(),'entite:lecture');


$nbCollectivite = count($liste_collectivite);

if ( ! $liste_collectivite){
	header("Location: ". SITE_BASE . "/index.php");
	exit;
}
if (count($liste_collectivite) == 1){
	if ($liste_collectivite[0]['id_e'] == 0 ) {
		$entiteListe = new EntiteListe($sqlQuery);
		$liste_collectivite = $entiteListe->getAllCollectivite($offset,$search);
		$nbCollectivite = $entiteListe->getNbCollectivite($search);
	} else {
		header("Location: detail.php?id_e=".$liste_collectivite[0]['id_e']);	
		exit;
	}
}


$page_title = "Liste des collectivités";


if ($roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",0)){
	$nouveau_bouton_url = array("Importer" => "entite/import.php","Nouveau" => "entite/edition.php");
}

include( PASTELL_PATH ."/include/haut.php");

?>


<?php 
include( PASTELL_PATH ."/include/bas.php");
