<?php

require_once( dirname(__FILE__) . "/../init-authenticated.php");

require_once( PASTELL_PATH . "/lib/entite/EntiteListe.class.php");

$entiteListe = new EntiteListe($sqlQuery);

$page_title = "Liste des centres de gestion";
$nouveau_bouton_url = "entite/nouveau.php?type=centre_de_gestion";

include( PASTELL_PATH ."/include/haut.php");
?>


<div class="box_contenu clearfix">

<h2>Centre de gestion</h2>


<table class="tab_01">
	<tr>
		<th>Dénomination</th>
		<th>Siren</th>
	</tr>
<?php foreach($entiteListe->getAll(Entite::TYPE_CENTRE_DE_GESTION) as $i => $entite) : ?>
	<tr class='<?php echo $i%2?'bg_class_gris':'bg_class_blanc'?>'>
		<td><a href='entite/detail.php?siren=<?php echo $entite['siren']?>'><?php hecho($entite['denomination']) ?></a></td>
		<td><?php 
		echo $entite['siren'] ?></td>
	
	</tr>
<?php endforeach; ?>
</table>

</div>
<?php 
include( PASTELL_PATH ."/include/bas.php");
