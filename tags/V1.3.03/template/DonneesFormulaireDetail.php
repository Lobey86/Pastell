<?php  if (! $donneesFormulaire->isValidable()) :  ?>
	<div class="alert alert-error">
		<?php  echo $donneesFormulaire->getLastError(); ?>
	</div>
<?php endif; ?>
	
<table class='table table-striped'>
<?php foreach($fieldDataList as $displayField): ?>
		<tr>
			<th class="w300">
				<?php echo $displayField->getField()->getLibelle() ?>
			</th>
			<td>
				<?php foreach($displayField->getValue() as $num => $value) :?>
						<?php if ($displayField->isURL()) :?>
							<a href='<?php echo $displayField->getURL($recuperation_fichier_url, $num,$id_e)?>'>
						<?php endif;?>
							<?php if ($displayField->getField()->getType() == 'textarea') : ?>
								<?php echo nl2br(get_hecho($value)); ?>
							<?php else:?>
							<?php hecho($value);?>
							<?php endif;?>
							<br/>
						<?php if($displayField->isURL()):?>
							</a>
						<?php endif;?>
				<?php endforeach;?>
			</td>
		</tr>
<?php endforeach;?>
</table>