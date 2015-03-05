<a class='btn btn-mini' href='system/index.php?page_number=2'><i class='icon-circle-arrow-left'></i>Liste des flux</a>

<div class="box">
<h2>Validation du flux </h2>
<?php if($document_type_is_validate) : ?>
	<div class='alert alert-success'>Le fichier definition.yml définissant le flux est valide</div>
<?php else :?>
	<div class='alert alert-error'>
		Le fichier definition.yml contient <?php echo count($validation_error) ?> erreur(s)
	</div>
	<table class='table table-condensed'>
	<?php foreach($validation_error as $error):?>
		<tr>
			<td><?php echo $error ?></td>
		</tr>
	<?php endforeach;?>
	</table>
<?php endif;?>

</div>

<div class="box">
<h2>Description</h2>
<?php if ($description) :?>
	<?php echo nl2br($description)?>
<?php else: ?>
	<div class='alert'>Il n'y a pas de description pour ce flux.</div>
<?php endif;?>
</div>


<div class="box">
<h2>Connecteurs utilisés par ce flux :</h2>
<ul>
<?php foreach($all_connecteur as $connecteur):?>
<li><?php hecho($connecteur) ?></li>
<?php endforeach; ?>
</ul>

</div>

<div class="box">
<h2>Élements du formulaire</h2>
<table class='table table-striped'>
<tr>
	<th>Id</th>
	<th>Libellé</th>
	<th>Commentaire</th>	
</tr>
<?php foreach($formulaire_fields as $field_id => $fields_properties) : ?>
<tr>
	<td><?php hecho($field_id)?></td>
	<td><?php hecho($fields_properties['name'])?></td>
	<td><?php hecho(isset($fields_properties['commentaire'])?$fields_properties['commentaire']:"")?></td>	
</tr>
<?php endforeach;?>
</table>
</div>


<div class="box">
<h2>Action du flux </h2>
<table class='table table-striped'>
<tr>
	<th>Id</th>
	<th>Nom de l'action</th>
	<th>Nom de l'état</th>
	<th>Classe</th>
	<th>Action automatique</th>
	<th>Emplacement</th>
</tr>
<?php
foreach($all_action as $i => $action) : ?>
	<tr>
		<td><?php hecho($action['id'])?></td>
		<td>
			<?php if($action['do_name'] != $action['name']) :?>
				<?php hecho($action['do_name'])?>
			<?php else: ?>
				&nbsp;
			<?php endif;?>
		
		</td>
		<td><?php hecho($action['name'])?></td>
		<td><?php hecho($action['class'])?></td>
		<td><?php hecho($action['action_auto'])?></td>
		<td><?php hecho($action['path'])?></td>
	</tr>
<?php endforeach;?>
</table> 
</div>