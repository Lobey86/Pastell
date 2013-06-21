<form action='document/external-data-controler.php' method='post'>
	<input type='hidden' name='id_d' value='<?php echo $id_d?>' />
	<input type='hidden' name='id_e' value='<?php echo $id_e?>' />
	<input type='hidden' name='page' value='<?php echo $page?>' />
	<input type='hidden' name='field' value='<?php echo $field?>' />
	<select name='iparapheur_sous_type'>
	<?php foreach($sous_type as $num => $type_message) : ?>
		<option value='<?php hecho($type_message) ?>'><?php hecho($type_message)?></option>
	<?php endforeach; ?>
	</select>	
	<input type='submit' class='btn' value='Sélectionner'/>
</form>