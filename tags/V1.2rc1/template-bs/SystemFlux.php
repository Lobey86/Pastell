<div class="box">
<h2>Flux disponible sur la plateforme</h2>
<table class='table table-striped'>
<tr>
	<th class="w200">Nom symbolique</th>
	<th class="w200">Libellé</th>
	<th>Module valide</th>
</tr>
<?php $i=0; foreach($all_flux as $id_flux => $flux) : ?>
	<tr>
		<td><a href='system/flux.php?id=<?php hecho($id_flux) ?>'><?php hecho($id_flux); ?></a></td>
		<td><?php hecho($flux['nom']); ?></td>
		<td>
			<?php if (! $flux['is_valide']) : ?>
				<b><a  href='system/flux.php?id=<?php hecho($id_flux) ?>'>Erreur sur le flux !</a></b>
			<?php endif;?>		
		</td>
	</tr>
<?php endforeach;?>
</table>


</div>