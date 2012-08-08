<?php 


$entite = new Entite($sqlQuery,$id_e);

$infoCDG = $entite->getCDG();


$donneesFormulaire = $donneesFormulaireFactory->get($infoCDG,'collectivite-properties');
$classifCDG = $donneesFormulaire->get("classification_cdg");


$page_title = "Choix de la nomenclature";


include( PASTELL_PATH ."/include/haut.php");

?>
<a href='entite/edition-properties.php?id_e=<?php echo $id_e ?>&page=<?php echo $page ?>'>« Revenir à l'entité</a>
<br/><br/>


<div class="box_contenu clearfix">
<h2>Choix de la nomenclature CDG</h2>

<table>
	<?php 
	foreach ($classifCDG as $i => $info) : ?>
		<tr class='<?php echo $i%2?'bg_class_gris':'bg_class_blanc'?>'>
			<td class="w30">		
			<a href='entite/external-data-controler.php?id_e=<?php echo $id_e?>&page=<?php echo $page?>&field=<?php echo $field ?>&nomemclature_file=<?php hecho($info) ?>'><?php echo $info?></a>
			</td>		
		</tr>
	<?php endforeach;?>
	<tr>
		<td class="w30">		
			<a href='entite/external-data-controler.php?id_e=<?php echo $id_e?>&page=<?php echo $page?>&field=<?php echo $field ?>&nomemclature_file='>Supprimer le fichier </a>
		</td>
	</tr>
</table>

</div>

<?php 
include( PASTELL_PATH ."/include/bas.php");
