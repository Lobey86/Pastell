<?php

require_once( dirname(__FILE__) . "/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/entite/EntiteDetailHTML.class.php");
require_once( PASTELL_PATH . "/lib/entite/AgentListHTML.class.php");

require_once( PASTELL_PATH . "/lib/utilisateur/UtilisateurListeHTML.class.php");

require_once( PASTELL_PATH . '/lib/formulaire/AfficheurFormulaire.class.php');
require_once (PASTELL_PATH . "/lib/helper/date.php");
require_once( PASTELL_PATH . "/lib/helper/suivantPrecedent.php");


$recuperateur = new Recuperateur($_GET);
$id_e = $recuperateur->getInt('id_e',0);
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
	$page_title = "D�tail " . $info['denomination'];
} else {
	$page_title = "Utilisateurs globaux";
	$tab_number=1;
}

$has_many_collectivite = $roleUtilisateur->hasManyEntite($authentification->getId(),'entite:lecture');

include( PASTELL_PATH ."/include/haut.php");
?>
<?php include(PASTELL_PATH . "/include/bloc_message.php");?>

<?php if ($id_e && $info['type'] == Entite::TYPE_FOURNISSEUR) : ?>
<a href='entite/fournisseur.php'>� liste des fournisseurs</a>
<?php elseif ($has_many_collectivite ) :?>
<a href='entite/index.php'>� liste des collectivit�s</a>
<?php endif;?>
<br/><br/>

<?php $formulaire_tab = array("Informations g�n�rales","Utilisateurs","Connecteurs","Flux", "Agents")?>

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
	$objectInstancier->EntiteControler->listUtilisateur();
elseif($tab_number == 2) : $i=0;?>
<h2>Listes des connecteurs
<a href="connecteur/new.php?id_e=<?php echo $id_e?>&page=1&page_retour=2" class='btn_maj'>Nouveau</a>
</h2>

<table class="tab_01">
<tr>
			<th>Libell�</th>
			<th>Nom </th>
			<th>Type</th>
			<th>&nbsp;</th>
		</tr>
<?php foreach($objectInstancier->ConnecteurEntiteSQL->getAll($id_e) as $connecteur) : ?>
	<tr class='<?php echo ($i++)%2?'bg_class_gris':'bg_class_blanc'?>'>
		<td><?php hecho($connecteur['libelle']);?></td>
		<td><?php echo $connecteur['id_connecteur'];?></td>
		<td><?php echo $connecteur['type'];?></td>
		<td>
			<a class='btn' href='connecteur/edition.php?id_ce=<?php echo $connecteur['id_ce']?>'>Configurer</a>
		</td>
	</tr>
<?php endforeach;?>
</table>


<?php if($roleUtilisateur->hasDroit($authentification->getId(),"entite:lecture",$id_e)) : ?>
	<a href='connecteur/detail.php?id_e=<?php echo $id_e ?>'>Configurer les connecteurs (ancienne m�thode)</a>
<?php endif;?>

<?php elseif($tab_number==3) :

$all_flux = $objectInstancier->FluxDefinitionFiles->getAll();
$all_flux_entite = $objectInstancier->FluxEntiteSQL->getAll($id_e);
?>

<h2>Listes des flux</h2>
<table class="tab_01">
		<tr>
				<th>Flux</th>
				<th>Type de connecteur</th>
				<th>Connecteur</th>
				<th>&nbsp;</th>
		</tr>
<?php 
$i = 0;
foreach($all_flux as $id_flux => $flux_definition) : 
	$documentType = $objectInstancier->DocumentTypeFactory->getFluxDocumentType($id_flux);
	foreach($documentType->getConnecteur() as $j=>$connecteur_type) : 
	?>
	<tr class='<?php echo $i++%2?'bg_class_gris':'bg_class_blanc'?>'>
		<?php if ($j == 0) :?>
		<td class='bg_class_blanc' rowspan='<?php echo (count($documentType->getConnecteur()))?>'><strong><?php hecho($documentType->getName() );?></strong></td>
		<?php endif;?>
		<td><?php echo $connecteur_type;?></td>
		<td>
			<?php if (isset($all_flux_entite[$id_flux][$connecteur_type])) : ?>
				<?php hecho($all_flux_entite[$id_flux][$connecteur_type]['libelle']) ?>
				&nbsp;(<?php hecho($all_flux_entite[$id_flux][$connecteur_type]['id_connecteur']) ?>)
			<?php else:?>
			AUCUN
			<?php endif;?>	
		</td>
		<td>
			<a class='btn' href='flux/edition.php?id_e=<?php echo $id_e?>&flux=<?php hecho($id_flux)?>&type=<?php echo $connecteur_type ?>'>Choisir un connecteur</a>
		</td>
	</tr>
	<?php endforeach;?>
<?php endforeach;?>
</table>
<?php elseif($tab_number == 4) :

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
<div class='box_info'><p>Informations h�rit�es de <a href='entite/detail.php?id_e=<?php echo $id_ancetre?>'><?php echo $infoAncetre['denomination']?></a></p></div>
<?php endif;?>
<?php $agentListHTML->display($listAgent); ?>


<?php  endif;?>

</div>


<?php if($id_e && $info['type'] == Entite::TYPE_FOURNISSEUR): ?>
<a href='supprimer.php'>Redemander les informations</a>
<?php endif; ?>


<?php 
include( PASTELL_PATH ."/include/bas.php");
