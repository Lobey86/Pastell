
<div class="box">

<h2>Selectionner la ou les collectivités avec lesquelles vous partagerez vos informations</h2>

<form action='document/action.php' method='post'>
	<input type='hidden' name='id_d' value='<?php echo $id_d?>' />
	<input type='hidden' name='id_e' value='<?php echo $id_e?>' />
	<input type='hidden' name='action' value=<?php echo $action?> />

<table class="table table-striped">
	<tr>
		<th>&nbsp;</th>
		<th>Dénomination</th>
		<th>Siren</th>
		<th>Informations acceptées</th>
	</tr>
<?php 
foreach($collectivite_liste as $cpt => $entite) : 
	?>
	<tr>
		<td class="w30"><input type='checkbox' name='destinataire[]' id="label_denomination_<?php echo $cpt ?>" value='<?php echo $entite['id_e']?>'/></td>
		<td><label for="label_denomination_<?php echo $cpt ?>">	<a href='entite/detail.php?id_e=<?php echo $entite['id_e']?>'><?php echo $entite['denomination']?></a></label></td>
		<td>
			<?php echo $entite['siren']?:""?>
		</td>
		<td>
		<?php echo $entite['is_valid']?"OUI":"NON";?>
		</td>

	</tr>
<?php endforeach; ?>
</table>

<input type='submit' value='Soumettre les informations' class='btn' />

</form>
</div>
