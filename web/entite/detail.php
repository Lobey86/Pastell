<?php

require_once( dirname(__FILE__) . "/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/entite/EntiteDetailHTML.class.php");
require_once( PASTELL_PATH . "/lib/entite/AgentListHTML.class.php");

require_once( PASTELL_PATH . "/lib/utilisateur/UtilisateurListeHTML.class.php");

require_once( PASTELL_PATH . '/lib/formulaire/AfficheurFormulaire.class.php');
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

<?php $formulaire_tab = array("Informations générales","Utilisateurs","Agent")?>

<div id="bloc_onglet">
		<?php foreach ($formulaire_tab as $page_num => $name) : ?>
					<a href='entite/detail.php?id_e=<?php echo $id_e ?>&page=<?php echo $page_num?>' <?php echo ($page_num == $tab_number)?'class="onglet_on"':'' ?>>
					<?php echo $name?>
					</a>
		<?php endforeach;?>
		</div>
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
	$entiteProperties = new EntitePropertiesSQL($sqlQuery);
	
	$entiteDetailHTML->display($info,$entiteProperties);
elseif($tab_number == 1) : 
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
 endif;?>

</div>


<?php if($id_e && $info['type'] == Entite::TYPE_FOURNISSEUR): ?>
<a href='supprimer.php'>Redemander les informations</a>
<?php endif; ?>

<?php if($roleUtilisateur->hasDroit($authentification->getId(),"entite:lecture",$id_e)) : ?>
	<a href='connecteur/detail.php?id_e=<?php echo $id_e ?>'>Configurer les connecteurs</a>
<?php endif;?>


<?php 
include( PASTELL_PATH ."/include/bas.php");
