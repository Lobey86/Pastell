<div class="box">

<table style='width:100%;'>
<tr>
<td>
<h2>Extensions installées</h2>
</td>
<?php if ($droitEdition) : ?>
<td class='align_right'>
	<a href="system/extension-edition.php" class='btn'>Nouveau</a>
</td>
<?php endif;?>
</tr>
</table>

<table class='table table-striped'>
<tr>
	<th class="w200">Nom symbolique</th>
	<th class="w200">Emplacement</th>
	<th>Connecteurs</th>
	<th>Flux</th>
</tr>
<?php $i=0; foreach($all_extensions as $id_e => $extension) : ?>
	<tr>
		<td><a href='system/extension.php?id_extension=<?php hecho($id_e) ?>'><?php hecho($extension['nom']); ?></a></td>
		<td><?php hecho($extension['path']); ?></td>
		<td>
			<ul>
			<?php foreach($extension['connecteur'] as $connecteur) : ?>
				<li><?php hecho($connecteur)?></li>
			<?php endforeach;?>
			</ul>
		</td>
		<td>
			<ul>
			<?php foreach($extension['flux'] as $flux) : ?>
				<li>
				<?php hecho($flux)?>
				</li>
			<?php endforeach;?>
			</ul>
		</td>
	</tr>
<?php endforeach;?>
</table>


</div>