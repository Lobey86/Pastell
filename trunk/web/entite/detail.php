<?php

require_once( dirname(__FILE__) . "/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/flux/FluxInscriptionFournisseur.class.php");
require_once( PASTELL_PATH . "/lib/utilisateur/UtilisateurListe.class.php");
require_once( PASTELL_PATH . "/lib/transaction/TransactionFinder.class.php");
require_once( PASTELL_PATH . "/lib/entite/EntiteProperties.class.php");
require_once( PASTELL_PATH . "/lib/entite/EntiteDetailHTML.class.php");
require_once( PASTELL_PATH . "/lib/utilisateur/UtilisateurListeHTML.class.php");

require_once( PASTELL_PATH . '/lib/formulaire/AfficheurFormulaire.class.php');
require_once (PASTELL_PATH . "/lib/action/ActionPossible.class.php");
require_once (PASTELL_PATH . "/lib/action/DocumentActionEntite.class.php");
require_once (PASTELL_PATH . "/lib/document/DocumentEntite.class.php");
require_once (PASTELL_PATH . "/lib/helper/date.php");

$recuperateur = new Recuperateur($_GET);
$id_e = $recuperateur->getInt('id_e');
$tab_number = $recuperateur->getInt('page',0);

$droit_lecture = $roleUtilisateur->hasDroit($authentification->getId(),"entite:lecture",$id_e);

if ( ! $droit_lecture ){
	header("Location: index.php");
	exit;
}

$entite = new Entite($sqlQuery,$id_e);
if ($id_e && ! $entite->exists()){
	header("Location: index.php");
	exit;
}

$info = $entite->getExtendedInfo();



$lastTransaction = false;
if ($id_e && $info['type'] == Entite::TYPE_FOURNISSEUR) {
	$transactionFinder = new TransactionFinder($sqlQuery);
	$lastTransaction = $transactionFinder->getLastTransactionBySiren($siren,FluxInscriptionFournisseur::TYPE);
}
if ($id_e){
	$page_title = "Détail " . $info['denomination'];
} else {
	$page_title = "Utilisateurs globaux";
	$tab_number=1;
}

$entiteProperties = new EntiteProperties($sqlQuery,$id_e);

$liste_collectivite = $roleUtilisateur->getEntite($authentification->getId(),'entite:lecture');
$has_many_collectivite = true;


if (count($liste_collectivite) == 1){
	if ($liste_collectivite[0] != 0) {
		$has_many_collectivite = false;
	}
}

$entiteDetailHTML = new EntiteDetailHTML();
if ($roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$id_e)){
	$entiteDetailHTML->addDroitEdition();
}

if (isset($info['cdg']['id_e']) && $roleUtilisateur->hasDroit($authentification->getId(),"entite:lecture",$info['cdg']['id_e'])){
	$entiteDetailHTML->addDroitLectureCDG();
}

include( PASTELL_PATH ."/include/haut.php");
?>
<?php include(PASTELL_PATH . "/include/bloc_message.php");?>

<?php if ($id_e && $info['type'] == Entite::TYPE_FOURNISSEUR) : ?>
<a href='entite/fournisseur.php'>« liste des fournisseurs</a>
<?php elseif ($has_many_collectivite ) :?>
<a href='entite/index.php'>« liste des collectivités</a>
<?php endif;?>
<br/><br/>

<?php 

if ($id_e  && $info['type'] != Entite::TYPE_FOURNISSEUR) {

	$documentType = $documentTypeFactory->getDocumentType('collectivite-properties');
	$formulaire = $documentType->getFormulaire();
	
	$donneesFormulaire = $donneesFormulaireFactory->get($id_e,'collectivite-properties');
	
	
	$afficheurFormulaire = new AfficheurFormulaire($formulaire,$donneesFormulaire);
	$afficheurFormulaire->injectHiddenField("id_e",$id_e);
	
	$afficheurFormulaire->afficheTab($tab_number,"entite/detail.php?id_e=$id_e");
}	
?>
<div class="box_contenu clearfix">

<?php if ($tab_number == 0) : 
	$entiteDetailHTML->display($info,$entiteProperties);
elseif($tab_number == 1) : 
	$utilisateurListe = new UtilisateurListe($sqlQuery);

	$utilisateurListeHTML = new UtilisateurListeHTML();
	if ($roleUtilisateur->hasDroit($authentification->getId(),"utilisateur:edition",$id_e)){
		$utilisateurListeHTML->addDroitEdition();
	}
	
	$utilisateurListeHTML->display($utilisateurListe->getUtilisateurByEntite($id_e),$id_e);
	
else: 


$theAction = $documentType->getAction();

$actionPossible = new ActionPossible($sqlQuery,$id_e,$authentification->getId(),$theAction);
//$actionPossible->setDocumentActionEntite($documentActionEntite);
//$actionPossible->setDocumentEntite($documentEntite);
$actionPossible->setRoleUtilisateur($roleUtilisateur);
$actionPossible->setDonnesFormulaire($donneesFormulaire);
$actionPossible->setEntite($entite);


?>
	<?php if ($roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$id_e)) : ?>
	<h2><a href="entite/edition-properties.php?id_e=<?php echo $id_e?>&page=<?php echo $tab_number ?>" class='btn_maj'>
			Modifier
		</a></h2>
	<?php endif;?>

	<?php $afficheurFormulaire->afficheStatic($tab_number,"document/recuperation-fichier.php?id_d=$id_e"); ?>
	
<br/>
<?php 



foreach($actionPossible->getActionPossible($id_e) as $action_name) : 

if ($formulaire->getTabName($tab_number) != $theAction->getProperties($action_name,"tab") ){
	continue;
}
?>
<form action='entite/action.php' method='post' >
	<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />
	<input type='hidden' name='page' value='<?php echo $tab_number ?>' />
	
	<input type='hidden' name='action' value='<?php echo $action_name ?>' />
	<input type='submit' value='<?php hecho($theAction->getActionName($action_name)) ?>'/>
</form>
<?php endforeach;?>
<?php endif;?>

</div>


<?php if($id_e && $info['type'] == Entite::TYPE_FOURNISSEUR): ?>
<a href='supprimer.php'>Redemander les informations</a>
<?php endif; ?>

<?php 
include( PASTELL_PATH ."/include/bas.php");
