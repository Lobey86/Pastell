<?php
$collectivite_id = 2;


require_once(PASTELL_PATH . "/lib/system/WebGFC.class.php");
$webGFC = new WebGFC();

$message_type = $webGFC->getInfo($recuperateur->get('messagetype'));


$entite = new Entite($sqlQuery,$id_e);
$infoEntite = $entite->getInfo();

$infoTypes = $webGFC->getTypes($collectivite_id);
if ($message_type){
	$infoSousTypes = $webGFC->getSousTypes($collectivite_id,$message_type[1]);
}

$page_title = "Choix d'un type de message";
include( PASTELL_PATH ."/include/haut.php");
?>

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
			<option value='<?php hecho($webGFC->setInfo($num,$type_message)) ?>'><?php echo $type_message?></option>
	<?php endforeach; ?>
	</select>
	<?php else : ?>
	<input type='hidden' name='messagetype' value='<?php hecho($webGFC->setInfo($message_type[0],$message_type[1]))?>'/>
 	Vous avez séléctionner <b><?php echo $message_type[1]?></b> (<a href='<?php echo "document/external-data.php?id_d=$id_d&id_e=$id_e&page=$page&field=$field" ?>'>Annuler</a>)
	<br/><br/><br/>
	Veuillez sélectionner le sous type : 
	<br/><br/><br/>
	<select name='messagesoustype'>
	<?php foreach($infoSousTypes as $num => $type_message) : ?>
			<option value='<?php hecho ($webGFC->setInfo($type_message[0],$type_message[1])) ?>'><?php echo utf8_decode($type_message[1])?></option>
	<?php endforeach; ?>
	</select>
	<?php endif;?>
	
	<input type='submit' value='Sélectionner'/>
</form>

<?php 
include( PASTELL_PATH ."/include/bas.php");