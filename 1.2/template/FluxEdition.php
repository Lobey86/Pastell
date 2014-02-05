
<a href='entite/detail.php?id_e=<?php echo $id_e ?>&page=3'>« Revenir</a>
<br/><br/>

<div class="box_contenu clearfix">

<h2>Associer un connecteur</h2>
<form class="w700" action='flux/edition-controler.php' method='post' >
<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />
<input type='hidden' name='flux' value='<?php echo $flux ?>' />
<input type='hidden' name='type' value='<?php echo $type_connecteur ?>' />

<table >

<tr>
<th>Flux</th>
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
<input type='submit' value='Associer' />
</form>
</div>
<br/><br/>
<div class="box_contenu clearfix">

<h2>Supprimer l'association</h2>
<form class="w700" action='flux/supprimer-controler.php' method='post' >
<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />
<input type='hidden' name='flux' value='<?php echo $flux ?>' />
<input type='hidden' name='type' value='<?php echo $type_connecteur ?>' />
<input type='submit' value="Supprimer l'association" />

</form>

</div>