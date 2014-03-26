<h2>Associations connecteurs globaux</h2>

<table class="table table-striped">
		<tr>
				<th class="w200">Type de connecteur</th>
				<th class="w200">Connecteur</th>
				<th>&nbsp;</th>
		</tr>
<?php 
$i = 0;

foreach($all_connecteur_type as $connecteur_type => $global_connecteur) :

	
	?>
	<tr>
		<td><?php echo $connecteur_type;?></td>
		<td>
			<?php if ($global_connecteur) : ?>
			<a href='connecteur/edition.php?id_ce=<?php echo $all_flux_global[$connecteur_type]['id_ce'] ?>'><?php hecho($all_flux_global[$connecteur_type]['libelle']) ?></a>
				&nbsp;(<?php hecho($all_flux_global[$connecteur_type]['id_connecteur']) ?>)
			<?php else:?>
			AUCUN
			<?php endif;?>	
		</td>
		<td>
			<a class='btn btn-mini' href='flux/edition.php?id_e=<?php echo $id_e?>&type=<?php echo $connecteur_type ?>'>Choisir un connecteur</a>
		</td>
	</tr>
	<?php endforeach;?>

</table>