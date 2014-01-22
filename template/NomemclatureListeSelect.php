<a href='connecteur/edition-modif.php?id_ce=<?php echo $id_ce ?>'>« Revenir au connecteur</a>
<br/><br/>


<div class="box_contenu clearfix">
<h2>Choix de la nomenclature CDG</h2>

<table>
	<?php 
	foreach ($classifCDG as $i => $info) : ?>
		<tr class='<?php echo $i%2?'bg_class_gris':'bg_class_blanc'?>'>
			<td class="w30">		
			<a href='connecteur/external-data-controler.php?id_ce=<?php echo $id_ce?>&field=<?php echo $field ?>&nomemclature_file=<?php hecho($info) ?>'><?php echo $info?></a>
			</td>		
		</tr>
	<?php endforeach;?>
	<tr>
		<td class="w30">		
			<a href='connecteur/external-data-controler.php?id_ce=<?php echo $id_ce?>&field=<?php echo $field ?>&nomemclature_file='>Supprimer le fichier </a>
		</td>
	</tr>
</table>

</div>