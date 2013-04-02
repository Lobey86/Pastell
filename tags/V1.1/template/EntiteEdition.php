<?php if ($id_e) : ?>
	<a href='entite/detail.php?id_e=<?php echo $id_e?>'>
		« revenir à <?php echo $infoEntite['denomination']?>
	</a>
<?php elseif ($entite_mere) : ?>
	<a href='entite/detail.php?id_e=<?php echo $infoMere['id_e']?>'>
		« revenir à <?php echo $infoMere['denomination']?>
	</a>
<?php else: ?>
	<a href='entite/detail.php'>
		« revenir à la liste des collectivités
	</a>
<?php endif;?>
<br/><br/>


<div class="box_contenu clearfix">

<form class="w700" action="entite/edition-controler.php" method='post'>
<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />

<?php if ($entite_mere) : ?>
<input type='hidden' name='entite_mere' value='<?php echo $entite_mere ?>' />
<?php else: ?>
<input type='hidden' name='entite_mere' value='<?php echo $infoEntite['entite_mere'] ?>' />

<?php endif;?>

<table>
	<tr>
	<th>Type d'entité</th>
	<td><select name='type'>
	<?php foreach (array(Entite::TYPE_COLLECTIVITE, Entite::TYPE_CENTRE_DE_GESTION,Entite::TYPE_SERVICE) as $type) :?>
		<option value='<?php echo $type?>'
			 <?php echo $infoEntite['type'] == $type?'selected="selected"':''?>> 
		<?php echo Entite::getNom($type) ?> </option>	
		<?php endforeach;?>
		
	</select></td>
	</tr>
<tr>
<th><label for="denomination">Nom<span>*</span></label>
<p class='form_commentaire'>60 caractères max</p>
</th>

<td><input type="text" name="denomination" id="denomination" value='<?php echo $infoEntite['denomination']?>'/></td>
</tr>
<tr>
<th><label for="siren">SIREN<span>*</span></label>
<p class='form_commentaire'>9 caractères obligatoires </p>
<p class='form_commentaire'>obligatoire pour une collectivité</p></th>
<td>
	<input type="text" name="siren" id="siren" value='<?php echo $infoEntite['siren']?>'/></td>

</tr>

<tr>
	<th><label for="cdg">Centre de gestion</label></th>
	<td>
		<?php $this->render("CDGSelect"); ?>
	</td>
</tr>

<tr>
<th><label for="has_ged">Utilisation d'une GED</label></th>
<td>
	<select name='has_ged'>
	<?php 

		$option = array('non'=>'non','manuel' => 'oui, manuellement' , 'auto' => 'oui, automatiquement');
		foreach($option as $key => $values): ?>
			<option value='<?php echo $key ?>' <?php echo $infoEntite['has_ged'] == $key?'selected="selected"':''?>><?php echo $values?></option>
	<?php endforeach; ?>

	</select>
</tr>
<tr>
<th><label for="has_archivage">Utilisation d'un SAE</label></th>
<td>
	<select name='has_archivage'>
		<?php 
		foreach($option as $key => $values): ?>
			<option value='<?php echo $key ?>' <?php echo $infoEntite['has_archivage'] == $key?'selected="selected"':''?>><?php echo $values?></option>
	<?php endforeach; ?>
	</select>
</tr>
</table>
<span>*</span> champs obligatoires 

<div class="align_right">
<?php if ($id_e) : ?>
<input type="submit" value="Modifier" class="submit" />

<?php else : ?>
<input type="submit" value="Créer" class="submit" />
<?php endif;?>
</div>


</form>

</div>
<?php $this->render("SirenBox");?>
