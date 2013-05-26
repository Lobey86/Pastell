<a href='system/index.php?page_number=2'>« Liste des flux</a>

<div class="box_contenu clearfix">
<h2>Validation du flux </h2>
<?php if($document_type_is_validate) : ?>
	<div class='box_info'><br/>Le fichier definition.yml définissant le flux est valide<br/><br/></div>
<?php else :?>
	<div class='box_error'>
		<br/>
		Le fichier definition.yml contient <?php echo count($validation_error) ?> erreur(s)
		<br/><br/>
	</div>
	<table>
	<?php foreach($validation_error as $error):?>
		<tr>
			<td><?php echo $error ?></td>
		</tr>
	<?php endforeach;?>
	</table>
<?php endif;?>

</div>

<div class="box_contenu clearfix">
<h2>Action du flux </h2>
<table class='tab_04'>
<tr>
	<th>Nom de l'action</th>
	<th>Classe</th>
	<th>Emplacement</th>
</tr>
<?php foreach($all_action as $i => $action) : ?>
	<tr  class='<?php echo ($i++)%2?'bg_class_gris':'bg_class_blanc'?>'>
		<td><?php hecho($action['name'])?></td>
		<td><?php hecho($action['class'])?></td>
		<td><?php hecho($action['path'])?></td>
	</tr>
<?php endforeach;?>
</table> 
</div>