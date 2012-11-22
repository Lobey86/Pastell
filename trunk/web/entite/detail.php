<?php

require_once( dirname(__FILE__) . "/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/entite/EntiteDetailHTML.class.php");
require_once( PASTELL_PATH . "/lib/entite/AgentListHTML.class.php");

require_once( PASTELL_PATH . "/lib/utilisateur/UtilisateurListeHTML.class.php");

require_once( PASTELL_PATH . '/lib/formulaire/AfficheurFormulaire.class.php');
require_once (PASTELL_PATH . "/lib/action/ActionPossible.class.php");
require_once (PASTELL_PATH . "/lib/helper/date.php");
require_once( PASTELL_PATH . "/lib/helper/suivantPrecedent.php");


$recuperateur = new Recuperateur($_GET);
$id_e = $recuperateur->getInt('id_e');
$tab_number = $recuperateur->getInt('page',0);
$offset = $recuperateur->getInt('offset',0);
$droit = $recuperateur->get('droit','');
$descendance = $recuperateur->get('descendance','');

if (! $id_e){
	header("Location: detail0.php");
	exit;
}

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
$info = $entite->getInfo();


if ($id_e){
	$page_title = "Détail " . $info['denomination'];
} else {
	$page_title = "Utilisateurs globaux";
	$tab_number=1;
}

$has_many_collectivite = $roleUtilisateur->hasManyEntite($authentification->getId(),'entite:lecture');

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
	$entiteDetailHTML = new EntiteDetailHTML();
	if ($roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$id_e)){
		$entiteDetailHTML->addDroitEdition();
	}
	
	if (isset($info['cdg']['id_e']) && $roleUtilisateur->hasDroit($authentification->getId(),"entite:lecture",$info['cdg']['id_e'])){
		$entiteDetailHTML->addDroitLectureCDG();
	}
	$info = $entite->getExtendedInfo();
	$entiteProperties = new EntiteProperties($sqlQuery,$id_e);
	
	$entiteDetailHTML->display($info,$entiteProperties);
elseif($tab_number == 1) : 

	$roleDroit = new RoleDroit();
	$all_droit =  $roleDroit->getAllDroit();

	$utilisateurListe = new UtilisateurListe($sqlQuery);
	$utilisateurListeHTML = new UtilisateurListeHTML();
	$utilisateurListeHTML->addDroit($allDroit);
	
	if ($roleUtilisateur->hasDroit($authentification->getId(),"utilisateur:edition",$id_e)){
		$utilisateurListeHTML->addDroitEdition();
	}
	if ($descendance){
		$all_id_e = $entite->getDescendance($id_e);
	} else {
		$all_id_e = array($id_e);
	}
	
	if ($droit){
		$allUtilisateur = $utilisateurListe->getUtilisateurByEntiteAndDroit($all_id_e,$droit);
	} else {
		$allUtilisateur = $utilisateurListe->getUtilisateurByEntite($all_id_e);
	}
	
	$utilisateurListeHTML->display($allUtilisateur,$id_e,$droit,$descendance);

elseif($tab_number == 2) :

$id_ancetre = $entite->getCollectiviteAncetre($id_e);
if ($id_ancetre == $id_e){
	$siren = $info['siren'];
} else {
	$ancetre = new Entite($sqlQuery,$id_ancetre);
	$infoAncetre = $ancetre->getInfo();
	$siren = $infoAncetre['siren'];
}
$agentListHTML = new AgentListHTML();
$agentSQL = new AgentSQL($sqlQuery);

$nbAgent = $agentSQL->getNbAgent($siren);
$listAgent = $agentSQL->getBySiren($siren,$offset);

?>
<h2>Liste des agents
<?php if ($roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$id_e)) : ?>
<a href="entite/import.php?id_e=<?php echo $id_e?>&page=1&page_retour=2" class='btn_maj'>
		Importer
		</a>
<?php endif;?>
</h2>
<?php suivant_precedent($offset,AgentSQL::NB_MAX,$nbAgent,"entite/detail.php?id_e=$id_e&page=$tab_number"); ?>
<?php if ($id_ancetre != $id_e): ?>
<div class='box_info'><p>Informations héritées de <a href='entite/detail.php?id_e=<?php echo $id_ancetre?>'><?php echo $infoAncetre['denomination']?></a></p></div>
<?php endif;?>
<?php $agentListHTML->display($listAgent); ?>


<?php 
else: 
$allTab = $formulaire->getTab();
if (in_array($allTab[$tab_number],array('Agents','TdT','Signature','GED','SAE'))){
	$id_e_to_show = $entite->getCollectiviteAncetre($id_e);
	$entite_to_show = new Entite($sqlQuery,$id_e_to_show);
	$infoAncetre = $entite_to_show->getInfo();
	
	if ($id_e != $id_e_to_show){
		$donneesFormulaire = $donneesFormulaireFactory->get($id_e_to_show,'collectivite-properties');
		$afficheurFormulaire = new AfficheurFormulaire($formulaire,$donneesFormulaire);
		$afficheurFormulaire->injectHiddenField("id_e",$id_e_to_show);
	}
}else {
	$id_e_to_show = $id_e;
	$entite_to_show = $entite;
}

$theAction = $documentType->getAction();

$actionPossible = new ActionPossible($sqlQuery,$id_e_to_show,$authentification->getId(),$theAction);
$actionPossible->setRoleUtilisateur($roleUtilisateur);
$actionPossible->setDonnesFormulaire($donneesFormulaire);
$actionPossible->setEntite($entite_to_show);


?>
	<?php if ($id_e_to_show == $id_e &&  $roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$id_e_to_show)) : ?>
	<h2><a href="entite/edition-properties.php?id_e=<?php echo $id_e_to_show?>&page=<?php echo $tab_number ?>" class='btn_maj'>
			Modifier
		</a></h2>
	<?php endif;?>
	<?php if ($id_e_to_show != $id_e ) : ?>
		<div class='box_info'><p>Informations héritées de <a href='entite/detail.php?id_e=<?php echo $id_e_to_show?>'><?php echo $infoAncetre['denomination']?></a></p></div>
	
	<?php endif;?>
	<?php $afficheurFormulaire->afficheStatic($tab_number,"document/recuperation-fichier.php?id_d=$id_e_to_show&id_e=$id_e_to_show"); ?>
	
<br/>
<?php 


if ($id_e == $id_e_to_show) : 
foreach($actionPossible->getActionPossible($id_e_to_show) as $action_name) : 

	if ($formulaire->getTabName($tab_number) != $theAction->getProperties($action_name,"tab") ){
		continue;
	}
?>
<form action='entite/action.php' method='post' >
	<input type='hidden' name='id_e' value='<?php echo $id_e_to_show ?>' />
	<input type='hidden' name='page' value='<?php echo $tab_number ?>' />
	
	<input type='hidden' name='action' value='<?php echo $action_name ?>' />
	<input type='submit' value='<?php hecho($theAction->getActionName($action_name)) ?>'/>
</form>
<?php endforeach;?>
<?php endif;?>
<?php endif;?>

</div>


<?php if($id_e && $info['type'] == Entite::TYPE_FOURNISSEUR): ?>
<a href='supprimer.php'>Redemander les informations</a>
<?php endif; ?>

<?php 
include( PASTELL_PATH ."/include/bas.php");
