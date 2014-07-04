
<a class='btn btn-mini' href='entite/detail.php?id_e=<?php echo $id_e ?>&page=3'><i class='icon-circle-arrow-left'></i>Revenir</a>

<div class="box">

<h2>Associer un connecteur</h2>
<form action='flux/edition-controler.php' method='post' >
<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />
<input type='hidden' name='flux' value='<?php echo $flux ?>' />
<input type='hidden' name='type' value='<?php echo $type_connecteur ?>' />

<table class='table table-striped'>

<tr>
<th class='w200'>Flux</th>
<td>
	<?php if($id_e) : ?>
	<?php hecho($objectInstancier->DocumentTypeFactory->getFluxDocumentType($flux)->getName() );?>
	<?php else: ?>
		global
	<?php endif;?>	
</td>
</tr>
<tr>
<th>Type de connecteur nécessaire</th>
<td><?php hecho($type_connecteur )?></td>
</tr>
<tr>
<th>Connecteur</th>
<td><select name='id_ce'>
		<?php foreach($connecteur_disponible as $connecteur) : ?>
			<option value='<?php hecho($connecteur['id_ce'])?>'><?php hecho($connecteur['id_connecteur'])?> (<?php hecho($connecteur['libelle'])?>)</option>
		<?php endforeach;?>
	</select></td>
</tr>

</table>
<button type='submit' class='btn'><i class='icon-retweet'></i>&nbsp;Associer</button>
</form>
</div>

<div class="box">

<h2>Supprimer l'association</h2>
<form action='flux/supprimer-controler.php' method='post' >
<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />
<input type='hidden' name='flux' value='<?php echo $flux ?>' />
<input type='hidden' name='type' value='<?php echo $type_connecteur ?>' />
<button type='submit' class='btn btn-danger'>Supprimer l'association</button>


</form>

</div>