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
				<?php if($displayField->getField()->getVisionneuse()):?>
					<a class='visionneuse_link' href='document/visionneuse.php?id_e=<?php echo $id_e?>&id_d=<?php hecho($id_d)?>&field=<?php hecho($displayField->getField()->getName()) ?>'>Voir</a>
					<div class='visionneuse_result'></div>
					<script>
$(document).ready(function(){

	$(".visionneuse_result").hide();
	
	$('.visionneuse_link').click(function(){
		var link=$(this).attr('href');
		var visionneuse_result = $(this).next(".visionneuse_result");
		visionneuse_result.toggle();
			$.ajax({
				url: link,
				cache: false
			}).done(function( html ) {
				visionneuse_result.html( html );
			});
		return false;
	});


	
});
					</script>
					
				<?php endif;?>
			</td>
		</tr>
<?php endforeach;?>
</table>