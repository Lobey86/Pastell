
<div class="box_contenu clearfix">

<h2><?php echo $type ?></h2>

<form action='document/action.php' method='post'>
	<input type='hidden' name='id_d' value='<?php echo $id_d?>' />
	<input type='hidden' name='id_e' value='<?php echo $id_e?>' />
	<input type='hidden' name='action' value=<?php echo $action?> />

<table class="tab_01">
	<tr>
		<th>&nbsp;</th>
		<th>Dénomination</th>
		<th>Siren</th>
	</tr>
<?php 
$cpt = 0;
foreach($liste as $i => $entite) : 
	$cpt++;
	?>
	<tr class='<?php echo $i%2?'bg_class_gris':'bg_class_blanc'?>'>
		<td class="w30"><input type='checkbox' name='destinataire[]' id="label_denomination_<?php echo $cpt ?>" value='<?php echo $entite['id_e']?>'/></td>
		<td><label for="label_denomination_<?php echo $cpt ?>">	<a href='entite/detail.php?id_e=<?php echo $entite['id_e']?>'><?php echo $entite['denomination']?></a></label></td>
		<td>
			<?php echo $entite['siren']?:""?>
		</td>

	</tr>
<?php endforeach; ?>
</table>
<div class="align_right">
<input type='submit' value='Envoyer le document' class='submit' />
</div>
</form>
</div>
