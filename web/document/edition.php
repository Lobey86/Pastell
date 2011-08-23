<?php

require_once(dirname(__FILE__)."/../init-authenticated.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");

require_once (PASTELL_PATH . "/lib/document/Document.class.php");

require_once( PASTELL_PATH . '/lib/formulaire/AfficheurFormulaire.class.php');
require_once( PASTELL_PATH . '/lib/formulaire/DataInjector.class.php');
require_once (PASTELL_PATH . "/lib/action/ActionPossible.class.php");
require_once (PASTELL_PATH . "/lib/action/ActionCreator.class.php");


$recuperateur = new Recuperateur($_GET);
$id_d = $recuperateur->get('id_d');
$type = $recuperateur->get('type');
$id_e = $recuperateur->getInt('id_e');
$page = $recuperateur->getInt('page',0);

$document = new Document($sqlQuery);

if ($id_d){
	$info = $document->getInfo($id_d);
	$type = $info['type'];
	$action = 'modification';
} else {
	$info = array();
	$id_d = $document->getNewId();	
	$document->save($id_d,$type);

	$documentEntite = new DocumentEntite($sqlQuery);
	$documentEntite->addRole($id_d,$id_e,"editeur");
	$actionCreator = new ActionCreator($sqlQuery,$journal,$id_d);
	$actionCreator->addAction($id_e,$authentification->getId(),Action::CREATION,"Création du document");
	
	$action = 'modification';
}

if ( ! $roleUtilisateur->hasDroit($authentification->getId(),$type.":edition",$id_e)) {
	header("Location: list.php");
	exit;
}

$documentType = $documentTypeFactory->getDocumentType($type);
$formulaire = $documentType->getFormulaire();

$entite = new Entite($sqlQuery,$id_e);
$infoEntite = $entite->getInfo();

$donneesFormulaire = $donneesFormulaireFactory->get($id_d,$type);


$theAction = $documentType->getAction();

$id_e_col = $entite->getCollectiviteAncetre();
$collectiviteProperties = $donneesFormulaireFactory->get($id_e_col,'collectivite-properties');

$actionPossible = new ActionPossible($sqlQuery,$id_e,$authentification->getId(),$theAction);
$actionPossible->setRoleUtilisateur($roleUtilisateur);
$actionPossible->setDonnesFormulaire($donneesFormulaire);
$actionPossible->setEntite($entite);
$actionPossible->setHeritedProperties($collectiviteProperties);

if ( ! $actionPossible->isActionPossible($id_d,$action)) {
	$lastError->setLastError("L'action « $action »  n'est pas permise : " .$actionPossible->getLastBadRule() );
	header("Location: detail.php?id_d=$id_d&id_e=$id_e&page=$page");
	exit;
}

$page_title="Edition d'un document « " . $documentType->getName() . " » ( " . $infoEntite['denomination'] . " ) ";


$dataInjector = new DataInjector($formulaire,$donneesFormulaire);
$dataInjector->inject($entite->getSiren());

$afficheurFormulaire = new AfficheurFormulaire($formulaire,$donneesFormulaire);
$afficheurFormulaire->injectHiddenField("id_d",$id_d);
$afficheurFormulaire->injectHiddenField("form_type",$type);
$afficheurFormulaire->injectHiddenField("id_e",$id_e);


include( PASTELL_PATH ."/include/haut.php");
include(PASTELL_PATH . "/include/bloc_message.php");
?>
<?php if ($info) : ?>
<a href='document/detail.php?id_d=<?php echo $id_d?>&id_e=<?php echo $id_e?>&page=<?php echo $page?>'>« <?php echo $info['titre']? $info['titre']:$info['id_d']?></a>
<?php else : ?>
<a href='document/list.php?type=<?php echo $type ?>&id_e=<?php echo $id_e?>'>« Liste des documents <?php echo $documentType->getName($type);  ?></a>
<?php endif;?>
<br/><br/>

<?php 
	if ($formulaire->getNbPage() > 1 ) {
		$afficheurFormulaire->afficheStaticTab($page);
	}
?>

<div class="box_contenu clearfix">
<?php $afficheurFormulaire->affiche($page,"document/edition-controler.php",
			"document/recuperation-fichier.php?id_d=$id_d&id_e=$id_e",
			"document/supprimer-fichier.php?id_d=$id_d&id_e=$id_e&page=$page",
			"document/external-data.php"
			); ?>
</div>

<?php 
include( PASTELL_PATH ."/include/bas.php");
