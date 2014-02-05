
<form action='document/external-data-controler.php' method='post'>
	<input type='hidden' name='id_d' value='<?php echo $id_d?>' />
	<input type='hidden' name='id_e' value='<?php echo $id_e?>' />
	<input type='hidden' name='page' value='<?php echo $page?>' />
	<input type='hidden' name='field' value='<?php echo $field?>' />
	<?php if (! $message_type ) : ?>
	Veuillez choisir un type de message : 
	<br/><br/>
	<select name='messagetype'>
	<?php foreach($infoTypes as $num => $type_message) : ?>
			<option value='<?php hecho($webGFC->setInfo($num,$type_message)) ?>'><?php echo utf8_decode($type_message) ?></option>
	<?php endforeach; ?>
	</select>
	<?php else : ?>
	<input type='hidden' name='messagetype' value='<?php hecho($webGFC->setInfo($message_type[0],$message_type[1]))?>'/>
 	Vous avez séléctionner <b><?php echo $message_type[1]?></b> (<a href='<?php echo "document/external-data.php?id_d=$id_d&id_e=$id_e&page=$page&field=$field" ?>'>Annuler</a>)
	<br/><br/><br/>
	Veuillez sélectionner le sous type : 
	<br/><br/><br/>
	<select name='messagesoustype'>
	<?php 
	foreach($infoSousTypes as $num => $type_message) : ?>
			<option value='<?php hecho ($webGFC->setInfo($num,$type_message)) ?>'><?php echo utf8_decode($type_message) ?></option>
	<?php endforeach; ?>
	</select>
	<?php endif;?>
	
	<input type='submit' value='Sélectionner'/>
</form>
