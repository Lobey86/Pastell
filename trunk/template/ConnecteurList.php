<table style='width:100%;'>
<tr>
<td>
<h2>Listes des connecteurs</h2>
</td>

<td class='align_right'>
<?php if ($droit_edition) : ?>
<a href="connecteur/new.php?id_e=<?php echo $id_e?>" class='btn'>Nouveau</a>
<?php endif;?>
</td>

</tr>
</table>



<table class="table table-striped">
<tr>
			<th>Instance</th>
			<th>Connecteur</th>
			<th>Famille de connecteur</th>
			<th>&nbsp;</th>
		</tr>
<?php foreach($all_connecteur as $i => $connecteur) : ?>
	<tr>
		<td><?php hecho($connecteur['libelle']);?></td>
		<td><?php echo $connecteur['id_connecteur'];?></td>
		<td><?php echo $connecteur['type'];?></td>
		<td>
			<a class='btn btn-mini' href='connecteur/edition.php?id_ce=<?php echo $connecteur['id_ce']?>'>Configurer</a>
		</td>
	</tr>
<?php endforeach;?>
</table>
