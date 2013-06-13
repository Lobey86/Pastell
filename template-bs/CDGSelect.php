<select name="centre_de_gestion">
	<option>...</option>
	<?php foreach($allCDG as $cdg)  :?>
		<option  <?php echo $cdg_selected == $cdg['id_e']?'selected="selected"':''?> value='<?php echo $cdg['id_e']?>'><?php echo $cdg['denomination']?></option>
	<?php endforeach;?>
</select>