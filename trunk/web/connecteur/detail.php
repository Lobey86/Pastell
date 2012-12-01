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
	$page_title = "Configuration des connecteurs pour « {$info['denomination']} »";
} else {
	$page_title = "Configuration des connecteurs globaux";
}

$has_many_collectivite = $roleUtilisateur->hasManyEntite($authentification->getId(),'entite:lecture');

include( PASTELL_PATH ."/include/haut.php");
?>
<?php include(PASTELL_PATH . "/include/bloc_message.php");?>

<?php if($id_e): ?>
<a href='entite/detail.php?id_e=<?php echo $id_e?>'>« Revenir à <?php echo $info['denomination']?></a>
<?php else: ?>
<a href='entite/index.php'>« Revenir à la liste des collectivités</a>
<?php endif;?>

<br/><br/>

<?php 
$documentType = $documentTypeFactory->getEntiteConfig($id_e);
$formulaire = $documentType->getFormulaire();
	
$donneesFormulaire = $donneesFormulaireFactory->getEntiteFormulaire($id_e);
	
$afficheurFormulaire = new AfficheurFormulaire($formulaire,$donneesFormulaire);
$afficheurFormulaire->injectHiddenField("id_e",$id_e);
	
$afficheurFormulaire->afficheTab($tab_number,"connecteur/detail.php?id_e=$id_e");
	
?>
<div class="box_contenu clearfix">
<?php 
$allTab = $formulaire->getTab();
if (in_array($allTab[$tab_number],array('Agents','TdT','Signature','GED','SAE'))){
	$id_e_to_show = $entite->getCollectiviteAncetre($id_e);
	$entite_to_show = new Entite($sqlQuery,$id_e_to_show);
	$infoAncetre = $entite_to_show->getInfo();
	
	if ($id_e != $id_e_to_show){
		$donneesFormulaire = $donneesFormulaireFactory->getEntiteFormulaire($id_e_to_show);
		$afficheurFormulaire = new AfficheurFormulaire($formulaire,$donneesFormulaire);
		$afficheurFormulaire->injectHiddenField("id_e",$id_e_to_show);
	}
}else {
	$id_e_to_show = $id_e;
	$entite_to_show = $entite;
}

$theAction = $documentType->getAction();

$actionPossible = $objectInstancier->ActionPossible;


?>
	<?php if ($id_e_to_show == $id_e &&  $roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$id_e_to_show)) : ?>
	<h2><a href="connecteur/edition-properties.php?id_e=<?php echo $id_e_to_show?>&page=<?php echo $tab_number ?>" class='btn_maj'>
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
foreach($actionPossible->getActionPossible($id_e_to_show,$authentification->getId(),$id_e_to_show) as $action_name) : 

	if ($formulaire->getTabName($tab_number) != $theAction->getProperties($action_name,"tab") ){
		continue;
	}
?>
<form action='connecteur/action-old.php' method='post' >
	<input type='hidden' name='id_e' value='<?php echo $id_e_to_show ?>' />
	<input type='hidden' name='page' value='<?php echo $tab_number ?>' />
	
	<input type='hidden' name='action' value='<?php echo $action_name ?>' />
	<input type='submit' value='<?php hecho($theAction->getActionName($action_name)) ?>'/>
</form>
<?php endforeach;?>
<?php endif;?>

</div>
<?php 
include( PASTELL_PATH ."/include/bas.php");
