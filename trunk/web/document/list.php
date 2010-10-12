<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once (PASTELL_PATH . "/lib/document/DocumentEntite.class.php");

require_once (PASTELL_PATH . "/include/navigation_collectivite.php");

require_once (PASTELL_PATH . "/lib/action/ActionPossible.class.php");
require_once (PASTELL_PATH . "/lib/action/DocumentActionEntite.class.php");
require_once( PASTELL_PATH . "/lib/helper/suivantPrecedent.php");
require_once( PASTELL_PATH . "/lib/document/DocumentListAfficheur.class.php");
require_once( PASTELL_PATH . "/lib/action/Action.class.php");


$recuperateur = new Recuperateur($_GET);
$type = $recuperateur->get('type');
$id_e = $recuperateur->get('id_e',0);
$offset = $recuperateur->getInt('offset',0);

$limit = 20;


$documentType = $documentTypeFactory->getDocumentType($type);

$liste_collectivite = array();

if ($id_e == 0){
	$liste_collectivite = $roleUtilisateur->getEntite($authentification->getId(),$type.":lecture");

	if ( ! $liste_collectivite){
		header("Location: ". SITE_BASE . "/index.php");
		exit;
	}

	if (count($liste_collectivite) == 1){
		$id_e = $liste_collectivite[0];
	}
	
} else if  (! $roleUtilisateur->hasDroit($authentification->getId(),$type.":lecture",$id_e)){
	header("Location: ".SITE_BASE . "/index.php");
	exit;
}


$entite = new Entite($sqlQuery,$id_e);
$infoEntite = $entite->getInfo();

$documentEntite = new DocumentEntite($sqlQuery);

$page_title = "Liste des documents " . $documentType->getName();
if ($id_e){
	$page_title .= " pour " . $infoEntite['denomination'];
}

$documentActionEntite = new DocumentActionEntite($sqlQuery);
$theAction = $documentType->getAction();
$actionPossible = new ActionPossible($sqlQuery,$id_e,$authentification->getId(),$theAction);
$actionPossible->setDocumentActionEntite($documentActionEntite);
$actionPossible->setDocumentEntite($documentEntite);
$actionPossible->setRoleUtilisateur($roleUtilisateur);
$actionPossible->setEntite($entite);

if ($actionPossible->isCreationPossible()){
	$nouveau_bouton_url = "document/edition.php?type=$type&id_e=$id_e";
}

include( PASTELL_PATH ."/include/haut.php");


if ($id_e != 0) {
	

	$listDocument = $documentActionEntite->getListDocument($id_e , $type , $offset, $limit ) ;
	
	$count = $documentActionEntite->getNbDocument($id_e,$type);
	
	suivant_precedent($offset,$limit,$count,"document/list.php?id_e=$id_e&type=$type");

	$documentListAfficheur = new DocumentListAfficheur($documentTypeFactory);
	
	$documentListAfficheur->affiche($listDocument,$id_e);
	

}


if (!$id_e && ! $roleUtilisateur->hasDroit($authentification->getId(),"$type:lecture",$id_e) ){
	navigation_racine($liste_collectivite,"document/list.php?type=$type");
} else {
	navigation_collectivite($entite,"document/list.php?type=$type");
}
if ($id_e) : ?>
<a href='journal/index.php?id_e=<?php echo $id_e?>&type=<?php echo $type?>'>Voir le journal des évènements</a>
<br/><br/>
<?php 
endif;
include( PASTELL_PATH ."/include/bas.php");
