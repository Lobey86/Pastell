<?php
require_once( dirname(__FILE__) . "/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/entite/EntiteDetailHTML.class.php");
require_once( PASTELL_PATH . "/lib/entite/AgentListHTML.class.php");
require_once( PASTELL_PATH . "/lib/utilisateur/UtilisateurListeHTML.class.php");
require_once( PASTELL_PATH . '/lib/formulaire/AfficheurFormulaire.class.php');
require_once (PASTELL_PATH . "/lib/helper/date.php");
require_once( PASTELL_PATH . "/lib/helper/suivantPrecedent.php");


$recuperateur = new Recuperateur($_GET);
$tab_number = $recuperateur->getInt('page',0);
$offset = $recuperateur->getInt('offset',0);
$droit = $recuperateur->get('droit','');
$descendance = $recuperateur->get('descendance','');

$id_e=0;
$entite = new Entite($sqlQuery,$id_e);

$droit_lecture = $roleUtilisateur->hasDroit($authentification->getId(),"entite:lecture",$id_e);

if ( ! $droit_lecture ){
	header("Location: index.php");
	exit;
}


$page_title = "Propriétés globales";

$formulaire_tab = array("Utilisateurs","Annuaire globale","Connecteur globaux");

include( PASTELL_PATH ."/include/haut.php");
?>
<?php include(PASTELL_PATH . "/include/bloc_message.php");?>


<a href='entite/index.php'>« liste des collectivités</a>
<br/><br/>
	
	<div id="bloc_onglet">
		<?php foreach ($formulaire_tab as $page_num => $name) : ?>
					<a href='entite/detail0.php?id_e=<?php echo $id_e ?>&page=<?php echo $page_num?>' <?php echo ($page_num == $tab_number)?'class="onglet_on"':'' ?>>
					<?php echo $name?>
					</a>
		<?php endforeach;?>
		</div>
	<div class="box_contenu clearfix">
	
<?php 
if ($tab_number == 0):
	$objectInstancier->EntiteControler->listUtilisateur();

elseif ($tab_number == 1) : ?>
	<a href='mailsec/annuaire.php'>Annuaire global »</a>
<?php 

	
elseif($tab_number == 2) : $i=0;?>
<h2>Listes des connecteurs
<a href="connecteur/new.php?id_e=<?php echo $id_e?>&page=1&page_retour=2" class='btn_maj'>Nouveau</a>
</h2>

<table class="tab_01">
<tr>
			<th>Libellé</th>
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
<?php endif;?>
</div>

<?php 
include( PASTELL_PATH ."/include/bas.php");
