<?php 

require_once( dirname(__FILE__) . "/../init-authenticated.php");

require_once( PASTELL_PATH . "/lib/entite/EntiteListe.class.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/helper/suivantPrecedent.php");

$recuperateur = new Recuperateur($_GET);
$offset = $recuperateur->getInt('offset',0);
$limit = 20;


$entiteListe = new EntiteListe($sqlQuery);

$count = $entiteListe->countCollectivite();

$page_title = "Liste des collectivités";

if ($roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",0)){
	$nouveau_bouton_url = "entite/edition.php";
}

include( PASTELL_PATH ."/include/haut.php");
suivant_precedent($offset,$limit,$count);

?>


<div class="box_contenu clearfix">

<h2>Collectivités</h2>


<table class="tab_01">
	<tr>
		<th>Dénomination</th>
		<th>Siren</th>
		<th>Type</th>
	</tr>
<?php foreach($entiteListe->getCollectivite($offset,$limit) as $i => $entite) : ?>
	<tr class='<?php echo $i%2?'bg_class_gris':'bg_class_blanc'?>'>
		<td><a href='entite/detail.php?id_e=<?php echo $entite['id_e']?>'><?php hecho($entite['denomination']) ?></a></td>
		<td><?php 
		echo $entite['siren'] ?></td>
		<td>
			<?php echo Entite::getNom($entite['type']) ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>

</div>
<?php 
include( PASTELL_PATH ."/include/bas.php");
