

<?php foreach($flux_definition as $part => $properties) :?>
	<div class="box">
		<h2><?php hecho($part)?></h2>
		
		<?php if ( $properties['info'] ) : ?>
		<div class='alert alert-info'><?php hecho($properties['info'])?></div>
		<?php endif; ?>
		
		<h3>Clés possibles :</h3>
		<table class='table table-striped'>
			<tr>
				<th class="w140">Nom de la clé</th>
				<th class="w140">Type</th>
				<th>Note</th>
			</tr>
		<?php foreach($properties['possible_key']  as $key_name => $key_properties) : ?>
			<tr>
				<td><strong><?php hecho($key_name)?>
				<?php if (isset($key_properties['key_name'])):?>
				(<?php hecho($key_properties['key_name'])?>)
				<?php endif;?>
				</strong></td>
				<td><?php hecho($key_properties['type'])?>
				<?php if(isset($key_properties['choice'])) : ?>
					(<?php hecho(implode(',',$key_properties['choice']))?>)
				<?php endif;?>
				</td>
				<td><?php hecho($key_properties['info'])?></td>
				
			</tr>
		<?php endforeach;?>
		</table>
	</div>
<?php endforeach;?>
