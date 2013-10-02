<form action='connecteur/external-data-controler.php' method='post'>
	<input type='hidden' name='id_ce' value='<?php echo $id_ce?>' />
	<input type='hidden' name='field' value='<?php echo $field?>' />
	<select name='iparapheur_type'>
	<?php foreach($type_iparapheur as $num => $type_message) : ?>
		<option value='<?php hecho($type_message) ?>'><?php hecho($type_message)?></option>
	<?php endforeach; ?>
	</select>	
	<input type='submit' class='btn' value='Sélectionner'/>
</form>