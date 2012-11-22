<?php 

require_once( dirname(__FILE__) . "/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/helper/suivantPrecedent.php");

$recuperateur = new Recuperateur($_GET);

$offset = $recuperateur->getInt('offset',0);
if ($offset <0){
	$offset = 0;
}
$search = $recuperateur->get('search');


$liste_collectivite = $roleUtilisateur->getEntiteWithDenomination($authentification->getId(),'entite:lecture');


$nbCollectivite = count($liste_collectivite);

if ( ! $liste_collectivite){
	header("Location: ". SITE_BASE . "/index.php");
	exit;
}
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


$page_title = "Liste des collectivités";


if ($roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",0)){
	$nouveau_bouton_url = array("Importer" => "entite/import.php","Nouveau" => "entite/edition.php");
}

include( PASTELL_PATH ."/include/haut.php");

?>
<?php if ($search || $nbCollectivite > 20) : ?>
<div>
<form action='entite/index.php' method='get' >
	<input type='text' name='search' value='<?php echo $search?>'/>
	<input type='submit' value='Rechercher' />
</form>
</div>
<?php endif;?>
<?php 
suivant_precedent($offset,20,$nbCollectivite,"entite/index.php?search=$search");
?>


<div class="box_contenu clearfix">

<h2>Collectivités</h2>
	
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
	<p>Voir tous les <a href='utilisateur/index.php'>utilisateurs</a></p>
	
	<?php endif;?>
	<p>Voir les <a href='entite/agents.php'>agents</a></p>
</div>
<?php 
include( PASTELL_PATH ."/include/bas.php");
