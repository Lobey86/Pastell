<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once (PASTELL_PATH . "/lib/document/DocumentEntite.class.php");

require_once (PASTELL_PATH . "/lib/entite/NavigationEntite.class.php");

require_once (PASTELL_PATH . "/lib/action/ActionPossible.class.php");
require_once (PASTELL_PATH . "/lib/action/DocumentActionEntite.class.php");
require_once( PASTELL_PATH . "/lib/helper/suivantPrecedent.php");
require_once( PASTELL_PATH . "/lib/document/DocumentListAfficheur.class.php");
require_once( PASTELL_PATH . "/lib/action/Action.class.php");


$recuperateur = new Recuperateur($_GET);
$type = $recuperateur->get('type');
$id_e = $recuperateur->get('id_e',0);
$offset = $recuperateur->getInt('offset',0);
$search = $recuperateur->get('search');

$limit = 20;


$documentType = $documentTypeFactory->getDocumentType($type);

$liste_collectivite = $roleUtilisateur->getEntite($authentification->getId(),$type.":lecture");

if ( ! $liste_collectivite){
	header("Location: ". SITE_BASE . "/index.php");
	exit;
}

if (!$id_e && (count($liste_collectivite) == 1)){
		$id_e = $liste_collectivite[0];
}
	
if  (! $roleUtilisateur->hasDroit($authentification->getId(),$type.":lecture",$id_e)){
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

if ($id_e && $actionPossible->isCreationPossible()){
	$nouveau_bouton_url = "document/edition.php?type=$type&id_e=$id_e";
}

include( PASTELL_PATH ."/include/haut.php");


if ($id_e != 0) {
	
?>
<div>
<form action='document/list.php' method='get' >
	<input type='hidden' name='id_e' value='<?php echo $id_e?>'/>
	<input type='hidden' name='type' value='<?php echo $type?>'/>
	<input type='text' name='search' value='<?php echo $search?>'/>
	<input type='submit' value='Rechercher' />
</form>
</div>
<?php
	$listDocument = $documentActionEntite->getListDocument($id_e , $type , $offset, $limit,$search ) ;
	
	$count = $documentActionEntite->getNbDocument($id_e,$type,$search);
	
	suivant_precedent($offset,$limit,$count,"document/list.php?id_e=$id_e&type=$type");

	$documentListAfficheur = new DocumentListAfficheur($documentTypeFactory);
	
	$documentListAfficheur->affiche($listDocument,$id_e);
	

}

$navigationEntite = new NavigationEntite($id_e,$liste_collectivite);

$navigationEntite->affiche("document/list.php?type=$type");



if ($id_e) : ?>
<a href='journal/index.php?id_e=<?php echo $id_e?>&type=<?php echo $type?>'>Voir le journal des évènements</a>
<br/><br/>
<?php 
endif;
include( PASTELL_PATH ."/include/bas.php");
