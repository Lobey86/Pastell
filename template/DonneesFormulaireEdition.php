<?php
$page_number = $page;

$donneesFormulaire->getFormulaire()->setTabNumber($page_number);


$id_ce = $inject['id_ce'];
$id_d = $inject['id_d'];
$action = $inject['action'];
$id_e = $inject['id_e'];
?>
		<form action='<?php echo $action_url ?>' method='post' enctype="multipart/form-data">
			<input type='hidden' name='page' value='<?php echo $page_number?>' />
			<?php foreach($this->inject as $name => $value ) : ?>
				<input type='hidden' name='<?php hecho($name); ?>' value='<?php hecho($value); ?>' />
			<?php endforeach;?>
			
			<table class='table table-striped'>
			<?php foreach ($donneesFormulaire->getDisplayFields($my_role) as $field) :
						
				if ($field->getProperties('read-only') && $field->getType() == 'file'){
					continue;
				}
				
			
			?>
				<tr>
					<th class='w500'>
						<label for="<?php echo $field->getName() ?>"><?php echo $field->getLibelle() ?><?php if ($field->isRequired()) : ?><span class='obl'>*</span><?php endif;?></label>
						
						<?php if ($field->isMultiple()): ?>
							(plusieurs <?php echo ($field->getType() == 'file')?"ajouts":"valeurs" ?> possibles)
						<?php endif;?>
						<?php if ($field->getProperties('commentaire')) : ?>
							<p class='form_commentaire'><?php echo $field->getProperties('commentaire') ?></p>
						<?php endif;?>
					</th>
					<td> 
					
					<?php if ($field->getType() == 'checkbox') :?>
						<?php if ($field->getProperties('depend') && $this->donneesFormulaire->get($field->getProperties('depend'))) : 
						
						?>
							<?php foreach($this->donneesFormulaire->get($field->getProperties('depend')) as $i => $file) :  ?>
								<input type='checkbox' name='<?php echo $field->getName()."_$i"; ?>' id='<?php echo $field->getName()."_$i";?>' 
									<?php echo $this->donneesFormulaire->geth($field->getName()."_$i")?"checked='checked'":'' ?>
									<?php echo $donneesFormulaire->isEditable($field->getName())?:"disabled='disabled'" ?>
									/><?php echo $file ?> 
									<br/>
							<?php endforeach;?>
						<?php else:?>
							<input type='checkbox' name='<?php echo $field->getName(); ?>' id='<?php echo $field->getName();?>' 
									<?php echo $this->donneesFormulaire->geth($field->getName())?"checked='checked'":'' ?>
									<?php echo $donneesFormulaire->isEditable($field->getName())?:"disabled='disabled'" ?>
									/>
						<?php endif; ?>
					<?php elseif($field->getType() == 'textarea') : ?>
						<textarea class='textarea_affiche_formulaire' rows='10' cols='40' id='<?php echo $field->getName();?>'  name='<?php echo $field->getName()?>' <?php echo $donneesFormulaire->isEditable($field->getName())?:"disabled='disabled'" ?>><?php echo $this->donneesFormulaire->get($field->getName(),$field->getDefault())?></textarea>
					<?php elseif($field->getType() == 'file') :?>
							<?php if ($donneesFormulaire->isEditable($field->getName())) : ?>
								<?php if ( ( $field->isMultiple()) || (! $this->donneesFormulaire->get($field->getName()))) : ?>
									<input type='file' id='<?php echo $field->getName();?>'  name='<?php echo $field->getName()?>' />
								<?php endif; ?>
								<?php if ($field->isMultiple()): ?>
									<button type='submit' name='ajouter' class='btn' value='Ajouter'><i class='icon-plus'></i>Ajouter</button>
									<!--<input class='input_normal send_button' type='submit' name='ajouter' value='Ajouter' />-->
								<?php endif;?>
								<?php if ( ( $field->isMultiple()) || (! $this->donneesFormulaire->get($field->getName()))) : ?>
								<br/>
								<?php endif;?>
							<?php endif;?>
							<?php if ($this->donneesFormulaire->get($field->getName())) : 
									foreach($this->donneesFormulaire->get($field->getName()) as $num => $fileName ): ?>
											<a href='<?php echo $recuperation_fichier_url ?>&field=<?php echo $field->getName()?>&num=<?php echo $num ?>'><?php echo $fileName ?></a>
											&nbsp;&nbsp;
											<?php if ($donneesFormulaire->isEditable($field->getName())) : ?>
												<a style='margin:4px 0' class='btn btn-mini btn-danger' href='<?php echo $suppression_fichier_url ?>&field=<?php echo $field->getName() ?>&num=<?php echo $num ?>'>supprimer</a>
											<?php endif;?>
										<br/>
							<?php endforeach;?>
							<?php endif;?>
					<?php elseif(($field->getType() == 'select') && ! $field->getProperties('read-only')) : ?>
					
						<?php if ($field->getProperties('depend') && $this->donneesFormulaire->get($field->getProperties('depend'))) : 
						
						?>
							<?php foreach($this->donneesFormulaire->get($field->getProperties('depend')) as $i => $file) :  ?>

									<br/>
									<?php echo $file ?>  <select name='<?php echo $field->getName()."_$i";?>' <?php echo $donneesFormulaire->isEditable($field->getName()."_$i")?:"disabled='disabled'" ?>>
							<option value=''>...</option>
							<?php foreach($field->getSelect() as $value => $name) : ?>
								<option <?php 
									if ($this->donneesFormulaire->geth($field->getName()."_$i") == $value){
										echo "selected='selected'";
									}
								?> value='<?php echo $value ?>'><?php echo $name ?></option>
							<?php endforeach;?>
						</select>
							<?php endforeach;?>
					<?php else :?>
						<select name='<?php echo $field->getName()?>' <?php echo $donneesFormulaire->isEditable($field->getName())?:"disabled='disabled'" ?>>
							<option value=''>...</option>
							<?php foreach($field->getSelect() as $value => $name) : ?>
								<option <?php 
									if ($this->donneesFormulaire->geth($field->getName()) == $value){
										echo "selected='selected'";
									}
								?> value='<?php echo $value ?>'><?php echo $name ?></option>
							<?php endforeach;?>
						</select>
					<?php endif;?>
					<?php elseif ($field->getType() == 'externalData') :?>
						<?php if ($donneesFormulaire->isEditable($field->getName())) : ?>
							<?php if($id_ce) : ?>
								<a href='<?php echo  $externalDataURL ?>?id_ce=<?php echo $id_ce ?>&field=<?php echo $field->getName()?>'><?php echo $field->getProperties('link_name')?></a>
							<?php elseif($field->isEnabled($id_e,$id_d) && isset($id_e)) :?>
								<a href='<?php echo  $externalDataURL ?>?id_e=<?php echo $id_e ?>&id_d=<?php echo $id_d ?>&page=<?php echo $page_number?>&field=<?php echo $field->getName()?>'><?php echo $field->getProperties('link_name')?></a>
							<?php else:?>
								non disponible
							<?php endif;?>
						<?php endif;?>
						<?php echo $this->donneesFormulaire->get($field->getName())?>&nbsp;
					<?php elseif ($field->getType() == 'password') : ?>
						<input 	type='password' 	
								id='<?php echo $field->getName();?>' 
								name='<?php echo $field->getName(); ?>' 
								value='' 
								size='16'
								<?php echo $donneesFormulaire->isEditable($field->getName())?:"disabled='disabled'" ?>
						/>
					<?php elseif( $field->getType() == 'link') : ?>
						<?php if ($donneesFormulaire->isEditable($field->getName())) : ?>
							<a href='<?php echo SITE_BASE . $field->getProperties('script')?>?id_e=<?php echo $id_e?>'><?php echo $field->getProperties('link_name')?></a>
						<?php else: ?>
							<?php echo $field->getProperties('link_name')?>
						<?php endif;?>				
					<?php else : ?>
						<?php if ($field->getProperties('read-only')) : ?>
							<?php echo $this->donneesFormulaire->geth($field->getName())?>&nbsp;
							<input type='hidden' name='<?php echo $field->getName(); ?>' value='<?php echo $this->donneesFormulaire->geth($field->getName())?>'/>
						<?php elseif( $field->getType() == 'date') : ?>
							
						<input 	type='text' 	
								id='<?php echo $field->getName();?>' 
								name='<?php echo $field->getName(); ?>' 
								value='<?php echo date_iso_to_fr($this->donneesFormulaire->geth($field->getName(),$field->getDefault()))?>' 
								size='40'
								<?php echo $donneesFormulaire->isEditable($field->getName())?:"disabled='disabled'" ?>
								/>
														
							<script type="text/javascript">
						   		 jQuery.datepicker.setDefaults(jQuery.datepicker.regional['fr']);
								$(function() {
									$("#<?php echo $field->getName()?>").datepicker( { dateFormat: 'dd/mm/yy' });
									
								});
							</script>
						<?php else : ?>
						<input 	type='text' 	
								id='<?php echo $field->getName();?>' 
								name='<?php echo $field->getName(); ?>' 
								value='<?php echo $this->donneesFormulaire->geth($field->getName(),$field->getDefault())?>' 
								size='40'
								<?php echo $donneesFormulaire->isEditable($field->getName())?:"disabled='disabled'" ?>
								/>
						<?php endif;?>
						<?php if ($field->getProperties('autocomplete')) : ?>
						 <script>
 							 $(document).ready(function(){
									$("#<?php echo $field->getName();?>").autocomplete("<?php echo $field->getProperties('autocomplete')?>",  
											{multiple: true,
											cacheLength:0, 
											max: 20, 
											extraParams: { id_e: <?php echo $id_e?>},
											formatItem : format_item

									});
 							 });
						</script>
						<?php endif;?>
					<?php endif;?>						
					</td>
				</tr>				
			<?php 	endforeach; ?>
			</table>
		
			<?php if ($page_number > 0 ): ?>
				<input type='submit' name='precedent' class='btn' value='« Précédent' />
			<?php endif; ?>
			<input type='submit' name='enregistrer' class='btn' value='Enregistrer' />
			<?php if ( ($donneesFormulaire->getFormulaire()->getNbPage() > 1) && ($donneesFormulaire->getFormulaire()->getNbPage() > $page_number + 1)): ?>
				<input type='submit' name='suivant' class='btn' value='Suivant »' />
			<?php endif; ?>
		</form>