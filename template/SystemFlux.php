<div class="box_contenu clearfix">
<h2>Flux disponible sur la plateforme</h2>
<table class='tab_04'>
<tr>
	<th>Nom symbolique</th>
	<th>Libellé</th>
</tr>
<?php $i=0; foreach($all_flux as $id_flux => $flux) : ?>
	<tr class='<?php echo ($i++)%2?'bg_class_gris':'bg_class_blanc'?>'>
		<td><a href='system/flux.php?id=<?php hecho($id_flux) ?>'><?php hecho($id_flux); ?></a></td>
		<td><?php hecho($flux['nom']); ?></td>
	</tr>
<?php endforeach;?>
</table>


</div>