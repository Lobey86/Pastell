<?php 

require_once( dirname(__FILE__) . "/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/entite/EntiteListe.class.php");

$recuperateur = new Recuperateur($_GET);
$cherche = $recuperateur->get('cherche');
$entiteListe = new EntiteListe($sqlQuery);
$entiteListe->setFiltre($cherche);

$liste = $entiteListe->getAll(Entite::TYPE_FOURNISSEUR);

$page_title = "Liste des fournisseurs";

include( PASTELL_PATH ."/include/haut.php");
?>

<div class="box_contenu clearfix">
	<h2>Rechercher</h2>
	<form action='<?php echo SITE_BASE?>/entite/fournisseur.php' method='get'>
	<input type='text' class='w140' name='cherche' value='<?php echo $cherche ?>' />
	<input type='submit' value='Recherche' class='submit' />
	&nbsp;&nbsp;<a href='<?php echo SITE_BASE?>/entite/recherche-avance.php'>Recherche avancée</a>
	</form>
</div>


<div class="box_contenu clearfix">

<h2>Fournisseurs</h2>
<?php if ($cherche) :?>
Liste des fournisseur contenant le mot «<?php echo $cherche?>».
<a href='entite/fournisseur.php'>Afficher tout</a>
<?php endif;?>


<form action='flux/nouveau.php' method='post'>
<input type='hidden' name='flux' value='gf_devis' />
<table class="tab_01">
	<tr>
		<th>&nbsp;</th>
		<th>Dénomination</th>
		<th>Siren</th>
		<th>Etat</th>
	</tr>
<?php 
$cpt = 0;
foreach($liste as $i => $entite) : 
	$cpt++;
	?>
	<tr class='<?php echo $i%2?'bg_class_gris':'bg_class_blanc'?>'>
		<td class="w30"><input type='checkbox' name='destinataire[]' id="label_denomination_<?php echo $cpt ?>" value='<?php echo $entite['siren']?>'/></td>
		<td><label for="label_denomination_<?php echo $cpt ?>"><?php echo $entite['denomination']?></label></td>
		<td>
		<a href='entite/detail.php?siren=<?php echo $entite['siren']?>'><?php echo $entite['siren']?></a>
		</td>
		<td>
			<?php echo Entite::getChaineEtat($entite['etat']) ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<div class="align_right">
<input type='submit' value='Créer une demande de devis' class='submit' />
</div>
</form>
</div>



<?php 
include( PASTELL_PATH ."/include/bas.php");
