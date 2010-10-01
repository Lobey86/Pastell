<?php 

require_once( dirname(__FILE__) . "/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/entite/EntiteListe.class.php");

$liste_collectivite = $roleUtilisateur->getEntite($authentification->getId(),'entite:lecture');

if ( ! $liste_collectivite){
	header("Location: ". SITE_BASE . "/index.php");
	exit;
}

if (count($liste_collectivite) == 1){
	if ($liste_collectivite[0] == 0) {
		$entiteListe = new EntiteListe($sqlQuery);
		$liste_collectivite = $entiteListe->getAllCollectiviteId();
	} else {
		header("Location: detail.php?id_e=".$liste_collectivite[0]);	
		exit;
	}
}


$page_title = "Liste des collectivités";


if ($roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",0)){
	$nouveau_bouton_url = "entite/edition.php";
}

include( PASTELL_PATH ."/include/haut.php");
?>


<div class="box_contenu clearfix">

<h2>Collectivités</h2>


<table class="tab_01">
	<tr>
		<th>Dénomination</th>
		<th>Siren</th>
		<th>Type</th>
	</tr>
<?php foreach($liste_collectivite as $i=>$id_e) : 

	$entite = new Entite($sqlQuery,$id_e);
	$info = $entite->getInfo();
?>
	<tr class='<?php echo $i%2?'bg_class_gris':'bg_class_blanc'?>'>
		<td><a href='entite/detail.php?id_e=<?php echo $info['id_e']?>'><?php hecho($info['denomination']) ?></a></td>
		<td><?php 
		echo $info['siren'] ?></td>
		<td>
			<?php echo Entite::getNom($info['type']) ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>

</div>
<?php 
include( PASTELL_PATH ."/include/bas.php");
