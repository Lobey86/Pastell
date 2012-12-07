<?php
require_once( __DIR__ . "/../init-authenticated.php");
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
$search = $recuperateur->get('search','');

$id_e=0;
$entite = new Entite($sqlQuery,$id_e);

$droit_lecture = $roleUtilisateur->hasDroit($authentification->getId(),"entite:lecture",$id_e);

if ( ! $droit_lecture ){
	header("Location: index.php");
	exit;
}


$page_title = "Administration";

$formulaire_tab = array("Entités","Utilisateurs","Annuaire global","Connecteurs globaux");

include( PASTELL_PATH ."/include/haut.php");
?>
<?php include(PASTELL_PATH . "/include/bloc_message.php");?>


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
if ($tab_number == 0) :

$liste_collectivite = $roleUtilisateur->getEntiteWithDenomination($authentification->getId(),'entite:lecture');
$nbCollectivite = count($liste_collectivite);

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

?>

<h2>Liste des collectivités
<?php if ($roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",0)) : ?>
<p id="bloc_boutons">
<a href="entite/import.php" >
<img src="img/commun/picto_nouveau.png" alt="" class="absmiddle" />
Importer</a>
<a href="entite/edition.php" >
<img src="img/commun/picto_nouveau.png" alt="" class="absmiddle" />
Nouveau</a>
</p>
<?php endif;?>
</h2>
<div>
<form action='entite/detail0.php' method='get' >
	<input type='text' name='search' value='<?php echo $search?>'/>
	<input type='submit' value='Rechercher' />
</form>
</div>
<?php 
suivant_precedent($offset,20,$nbCollectivite,"entite/detail0.php?search=$search");
?>
	<table class="tab_01">
		<tr>
			<th>Dénomination</th>
			<th>Siren</th>
			<th>Type</th>
		</tr>
	<?php foreach($liste_collectivite as $i => $info) : ?>
		<tr class='<?php echo $i%2?'bg_class_gris':'bg_class_blanc'?>'>
			<td><a href='entite/detail.php?id_e=<?php echo  $info['id_e'] ?>'><?php hecho($info['denomination']) ?></a></td>
			<td><?php 
			echo $info['siren'] ?></td>
			<td>
				<?php echo Entite::getNom($info['type']) ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>

	
	<?php if ($roleUtilisateur->hasDroit($authentification->getId(),"utilisateur:lecture",0)) : ?>
	
	<p>Voir les <a href='entite/detail0.php'>Propriétés globales</a></p>	
	
	<?php endif;?>
	<p>Voir les <a href='entite/agents.php'>agents</a></p>

<?php 
elseif ($tab_number == 1):
	$objectInstancier->EntiteControler->listUtilisateur();

elseif ($tab_number == 2) : ?>
	<a href='mailsec/annuaire.php'>Annuaire global »</a>
<?php 

	
elseif($tab_number == 3) : $i=0;?>
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
