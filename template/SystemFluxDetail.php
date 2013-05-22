<a href='system/index.php?page_number=2'>« Liste des flux</a>
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