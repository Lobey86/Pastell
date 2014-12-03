<div class="box">
<h2>Connecteurs globaux disponibles sur la plateforme</h2>
<table class='table table-striped'>
<tr>
	<th class="w200">Nom symbolique</th>
	<th class="w200">Libellé</th>
	<th>Description</th>
</tr>
<?php foreach($all_connecteur_globaux as $id_connecteur => $connecteur) : ?>
	<tr>
		<td><?php hecho($id_connecteur); ?></td>
		<td><?php hecho($connecteur['name']); ?></td>
		<td><?php echo nl2br(isset($connecteur['description'])?$connecteur['description']:''); ?></td>
		<td>
			
		</td>
	</tr>
<?php endforeach;?>
</table>


</div>


<div class="box">
<h2>Connecteurs d'entité disponibles sur la plateforme</h2>
<table class='table table-striped'>
<tr>
	<th class="w200">Nom symbolique</th>
	<th class="w200">Libellé</th>
	<th>Description</th>
</tr>
<?php foreach($all_connecteur_entite as $id_connecteur => $connecteur) : ?>
	<tr>
		<td><?php hecho($id_connecteur); ?></td>
		<td><?php hecho($connecteur['name']); ?></td>
		<td><?php echo nl2br(isset($connecteur['description'])?$connecteur['description']:''); ?></td>
		<td>
			
		</td>
	</tr>
<?php endforeach;?>
</table>


</div>