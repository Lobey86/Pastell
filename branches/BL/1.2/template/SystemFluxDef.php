<div class="box_contenu clearfix">

<?php foreach($flux_definition as $part => $properties) :?>
	<h2><?php hecho($part)?></h2>
	<p><?php hecho($properties['info'])?></p>
	<em>Clés possibles :</em>
	<table class='tab_04'>
		<tr>
			<th>Nom de la clé</th>
			<th>Type</th>
			<th>Note</th>
		</tr>
	<?php foreach($properties['possible_key']  as $key_name => $key_properties) : ?>
		<tr>
			<td><strong><?php hecho($key_name)?></strong></td>
			<td><?php hecho($key_properties['type'])?>
			<?php if(isset($key_properties['choice'])) : ?>
				(<?php hecho(implode(',',$key_properties['choice']))?>)
			<?php endif;?>
			</td>
			<td><?php hecho($key_properties['info'])?></td>
			
		</tr>
	<?php endforeach;?>
	</table>
<?php endforeach;?>
</div>