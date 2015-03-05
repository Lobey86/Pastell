<form action='document/external-data-controler.php' method='post'>
	<input type='hidden' name='id_ce' value='<?php echo $id_ce?>' />
	<input type='hidden' name='id_d' value='<?php echo $id_d?>' />
	<input type='hidden' name='id_e' value='<?php echo $id_e?>' />
	<input type='hidden' name='page' value='<?php echo $page?>' />
	<input type='hidden' name='field' value='<?php echo $field?>' />
	<select name='choix_select'>
	<?php foreach($liste_elements as $num => $nom_element) : ?>
		<option value='<?php hecho($nom_element) ?>'><?php hecho($nom_element)?></option>
	<?php endforeach; ?>
	</select>	
	<input type='submit' class='btn' value='Sélectionner'/>
</form>