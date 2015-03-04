
<div class="box">

<h2>Sélectionner la collectivité sur laquelle vous déposerez votre facture </h2>


<table class="table table-striped">
	<tr>
		<th>Dénomination</th>
		<th>Siren</th>
	</tr>
<?php 
foreach($collectivite_info_list as $cpt => $entite) : 
	?>
	<tr>
		<td>
			<a href='<?php echo $entite['link']?>'>
			<label for="label_denomination_<?php echo $cpt ?>"><?php echo $entite['denomination']?></label>
			</a>
			</td>
			
		<td>
			<?php echo $entite['siren']?:""?>
		</td>

	</tr>
<?php endforeach; ?>
</table>

</div>
