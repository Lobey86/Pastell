<a class='btn btn-mini' href='system/index.php?page_number=4'>
	<i class='icon-circle-arrow-left'></i>Liste des extensions
</a>

<div class="box">
<table style='width:100%;'>
<tr>
<td>
<h2>Extension <?php hecho($extension_info['nom'])?></h2>
</table>


<?php if (! $extension_info['exists']) : ?>
	<div class='alert alert-error'>
		Impossible de lire l'extension sur le système de fichier
	</div>
<?php endif ?>


<?php if (! $extension_info['manifest']) : ?>
	<div class='alert'>
		Cette extension ne contient pas de fichier manifest.yml
	</div>
<?php else : ?>
<table class='table table-striped'>
<tr>
	<th>Nom</th>
	<td><?php hecho($extension_info['manifest']['nom']) ?></td>
</tr>
<tr>
	<th>Description</th>
	<td><?php hecho($extension_info['manifest']['description']) ?></td>
</tr>
<tr>
	<th>Version de Pastell attendue</th>
	<td><?php hecho($extension_info['manifest']['pastell-version']) ?></td>
</tr>
</table>
<?php endif;?> 
<a href='system/extension-edition.php?id_extension=<?php echo $extension_info['id_e']?>' class='btn'>Modifier</a>
<a href='system/extension-delete.php?id_e=<?php echo $extension_info['id_e']?>' class='btn btn-danger' onclick='return confirm("Êtes-vous sûr de vouloir supprimer cette extension ?")'>Supprimer</a>
</div>

<div class="box">
<h2>Connecteurs</h2>
<table class='table table-striped'>
<tr>
	<th>Nom</th>
	<th>Description</th>
</tr>
<?php foreach($extension_info['connecteur'] as $connecteur) : ?>
				<tr>
					<td><b><?php hecho($connecteur)?></b></td>
					<td>
					<?php 
					$connecteur_info = $this->ConnecteurDefinitionFiles->getInfo($connecteur);
					?>
					<?php if (isset($connecteur_info['description'])) : ?>
						<?php echo nl2br($connecteur_info['description']); ?>
					<?php endif;?>
					</td>
				</tr>
<?php endforeach;?>
</table>
</div>
		
<div class="box">
<h2>Flux</h2>
<table class='table table-striped'>
<tr>
	<th>Nom</th>
	<th>Description</th>
</tr>
<?php foreach($extension_info['flux'] as $flux) : ?>
				<tr>
					<td><b><?php hecho($flux)?></b></td>
					<td>
					<?php $flux_info = $this->FluxDefinitionFiles->getInfo($flux); ?>
					<?php if (isset($flux_info['description'])) : ?>
						<?php echo nl2br($flux_info['description']); ?>
					<?php endif;?>
					</td>
				</tr>
<?php endforeach;?>
</table>
</div>

		