<h2>Listes des connecteurs
<a href="connecteur/new.php?id_e=<?php echo $id_e?>" class='btn_maj'>Nouveau</a>
</h2>

<table class="tab_01">
<tr>
			<th>Libellé</th>
			<th>Nom </th>
			<th>Type</th>
			<th>&nbsp;</th>
		</tr>
<?php foreach($all_connecteur as $i => $connecteur) : ?>
	<tr class='<?php echo $i%2?'bg_class_gris':'bg_class_blanc'?>'>
		<td><?php hecho($connecteur['libelle']);?></td>
		<td><?php echo $connecteur['id_connecteur'];?></td>
		<td><?php echo $connecteur['type'];?></td>
		<td>
			<a class='btn' href='connecteur/edition.php?id_ce=<?php echo $connecteur['id_ce']?>'>Configurer</a>
		</td>
	</tr>
<?php endforeach;?>
</table>
