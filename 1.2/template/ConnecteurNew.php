
<a href='entite/detail.php?id_e=<?php echo $id_e ?>&page=3'>« Retour</a>
<br/><br/>

<div class="box_contenu clearfix">

<h2>Ajouter un connecteur</h2>
<form class="w700" action='connecteur/new-controler.php' method='post' >
<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />
<table >

<tr>
<th>Libellé</th>
<td><input type='text' name='libelle' value=''/></td>
</tr>

<tr>
<th>Connecteur</th>
<td><select name='id_connecteur'>
		<?php foreach($all_connecteur_dispo as $id_connecteur => $connecteur) : ?>
			<option value='<?php hecho($id_connecteur)?>'>
				<?php hecho($connecteur['name'])?> (<?php hecho($connecteur['type'])?>)
			</option>
		<?php endforeach;?>
	</select></td>
</tr>

</table>
<input type='submit' value='Créer un connecteur' />
</form>
</div>
<br/><br/>