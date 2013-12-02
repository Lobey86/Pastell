<?php
require_once(TEMPLATE_PATH."/FormulaireRenderer.class.php");

class AfficheurFormulaire {
	
	private $formulaire;
	private $donneesFormulaire;
	private $inject;
	private $onePage;
	
	private $role;
	
	private $editable_content;
	private $has_editable_content;
	
	private $formulaireRenderer;
	
	public function __construct(Formulaire $formulaire, DonneesFormulaire $donneesFormulaire){
		$this->formulaire = $formulaire;
		$this->donneesFormulaire = $donneesFormulaire;
		
		$this->formulaire->addDonnesFormulaire($donneesFormulaire);
		$this->inject = array();
		$this->formulaireRenderer = new FormulaireRenderer();
	}
	
	public function setEditableContent(array $editable_content){
		$this->has_editable_content = true;
		$this->editable_content = $editable_content;
	}
	
	public function setRole($role){
		$this->role = $role;
	}
	
	public function show(Field $field){
		
		if ($field->getProperties('no-show')){
			return false;
		}
		
		$show_role = $field->getProperties('show-role') ;
		
		if (! $show_role){
			return true;
		}
		
		foreach($show_role as $role){
			if ($role == $this->role){
				return true;
			}
		}
		
		return false;
		
	}
	
	public function isReadOnly($field_name){
		
		$field = $this->formulaire->getField($field_name);
		if (!$field){
			return false;
		}
		
		$read_only_content = $field->getProperties('read-only-content') ;
		if (!$read_only_content){
			return false;
		}	
		foreach($read_only_content as $key => $value){
			if ($this->donneesFormulaire->get($key) != $value){
				return false;
			}
		}
		return true;
	}
	
	public function isEditable($field_name){
		if ($this->isReadOnly($field_name)){
			return false;
		}
		if ( ! $this->has_editable_content){
			return true;
		}
		return in_array($field_name,$this->editable_content);
	}
	
	public function injectHiddenField($name,$value){
		$this->inject[$name] = $value;
	}

	public function afficheTab($tab_selected,$page_url){ 
		if ($this->formulaire->afficheOneTab()){
			return;
		}
		$this->formulaireRenderer->showOnglet($this->formulaire->getTab(),$tab_selected,$page_url);
	}
	
	public function afficheStaticTab($page){
		$this->formulaireRenderer->showOngletStatic($this->formulaire->getTab(),$page);


	}
	
	
	private function getInjectedField($name){
		if(isset($this->inject[$name])){
			return $this->inject[$name];
		}
		return false;
	}
	
	public function affiche($page_number,$action_url,$recuperation_fichier_url , $suppression_fichier_url,$externalDataURL ){

		$this->formulaire->setTabNumber($page_number);
		
		
		$id_d = $this->getInjectedField('id_d');
		$id_e = $this->getInjectedField('id_e');
		$id_ce = $this->getInjectedField('id_ce');
		$action = $this->getInjectedField('action');
		
		?>
		<form action='<?php echo $action_url ?>' method='post' enctype="multipart/form-data">
			<input type='hidden' name='page' value='<?php echo $page_number?>' />
			<?php foreach($this->inject as $name => $value ) : ?>
				<input type='hidden' name='<?php hecho($name); ?>' value='<?php hecho($value); ?>' />
			<?php endforeach;?>
			
			<table class='<?php echo FormulaireRenderer::TABLE_MODIF_CLASS ?>'>
			<?php foreach ($this->formulaire->getFields() as $field) :
						
				if ($field->getProperties('read-only') && $field->getType() == 'file'){
					continue;
				}
				if (! $this->show($field)){
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
									<?php echo $this->isEditable($field->getName())?:"disabled='disabled'" ?>
									/><?php echo $file ?> 
									<br/>
							<?php endforeach;?>
						<?php else:?>
							<input type='checkbox' name='<?php echo $field->getName(); ?>' id='<?php echo $field->getName();?>' 
									<?php echo $this->donneesFormulaire->geth($field->getName())?"checked='checked'":'' ?>
									<?php echo $this->isEditable($field->getName())?:"disabled='disabled'" ?>
									/>
						<?php endif; ?>
					<?php elseif($field->getType() == 'textarea') : ?>
						<textarea rows='10' cols='40' id='<?php echo $field->getName();?>'  name='<?php echo $field->getName()?>' <?php echo $this->isEditable($field->getName())?:"disabled='disabled'" ?>><?php echo $this->donneesFormulaire->get($field->getName(),$field->getDefault())?></textarea>
					<?php elseif($field->getType() == 'file') :?>
							<?php if ($this->isEditable($field->getName())) : ?>
								<?php if ( ( $field->isMultiple()) || (! $this->donneesFormulaire->get($field->getName()))) : ?>
									<input type='file' id='<?php echo $field->getName();?>'  name='<?php echo $field->getName()?>' />
								<?php endif; ?>
								<?php if ($field->isMultiple()): ?>
									<button type='submit' name='ajouter' class='<?php echo FormulaireRenderer::BUTTON_CLASS ?>' value='Ajouter'><i class='icon-plus'></i>Ajouter</button>
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
											<?php if ($this->isEditable($field->getName())) : ?>
												<a style='margin:4px 0' class='<?php echo FormulaireRenderer::BUTTON_CLASS ?> btn-mini btn-danger' href='<?php echo $suppression_fichier_url ?>&field=<?php echo $field->getName() ?>&num=<?php echo $num ?>'>supprimer</a>
											<?php endif;?>
										<br/>
							<?php endforeach;?>
							<?php endif;?>
					<?php elseif(($field->getType() == 'select') && ! $field->getProperties('read-only')) : ?>
					
						<?php if ($field->getProperties('depend') && $this->donneesFormulaire->get($field->getProperties('depend'))) : 
						
						?>
							<?php foreach($this->donneesFormulaire->get($field->getProperties('depend')) as $i => $file) :  ?>

									<br/>
									<?php echo $file ?>  <select name='<?php echo $field->getName()."_$i";?>' <?php echo $this->isEditable($field->getName()."_$i")?:"disabled='disabled'" ?>>
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
						<select name='<?php echo $field->getName()?>' <?php echo $this->isEditable($field->getName())?:"disabled='disabled'" ?>>
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
						<?php if ($this->isEditable($field->getName())) : ?>
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
								<?php echo $this->isEditable($field->getName())?:"disabled='disabled'" ?>
						/>
					<?php elseif( $field->getType() == 'link') : ?>
						<?php if ($this->isEditable($field->getName())) : ?>
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
								<?php echo $this->isEditable($field->getName())?:"disabled='disabled'" ?>
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
								<?php echo $this->isEditable($field->getName())?:"disabled='disabled'" ?>
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
		<?php if ($this->formulaire->hasRequiredField()): ?>
		<!--* champs obligatoires.<br/>-->
		<?php endif;?>
		
		<?php if ($page_number > 0 ): ?>
			
			<input type='submit' name='precedent' class='<?php echo FormulaireRenderer::BUTTON_CLASS ?>' value='« Précédent' />
			
		<?php endif; ?>
			
			<input type='submit' name='enregistrer' class='<?php echo FormulaireRenderer::BUTTON_CLASS ?>' value='Enregistrer' />
			
		<?php if ( ($this->formulaire->getNbPage() > 1) && ($this->formulaire->getNbPage() > $page_number + 1)): ?>
			
			<input type='submit' name='suivant' class='<?php echo FormulaireRenderer::BUTTON_CLASS ?>' value='Suivant »' />

			<?php endif; ?>
		</form>
	<?php }
	
	private function getFieldStatic(){
		if ($this->formulaire->afficheOneTab()){
			return $this->formulaire->getAllFields();
		}
		return $this->formulaire->getFields();
	}
	
	public function afficheStatic($page,$recuperation_fichier_url){
		
		if (isset($this->inject['id_e'])){
			$id_e = $this->inject['id_e'];
		}
		
		if (! $this->donneesFormulaire->isValidable()){
				$this->formulaireRenderer->alert($this->donneesFormulaire->getLastError());
		}
		
			$this->formulaire->setTabNumber($page);
		?>
		<table class='<?php echo FormulaireRenderer::TABLE_CLASS ?>'>
			<?php
			$i=0;
			foreach ($this->getFieldStatic() as $field) :
					if (! $this->show($field)){
						continue;
					}
								
			?>
				<?php $this->formulaireRenderer->formEntete($i++,$field->getLibelle()); ?>
					<td>
						<?php if ($field->getType() == 'checkbox') :?>
							<?php if ($field->getProperties('depend') && $this->donneesFormulaire->get($field->getProperties('depend'))) : ?>
								<?php foreach($this->donneesFormulaire->get($field->getProperties('depend')) as $i => $file) :  ?> 
										<?php echo $file ?> : <?php echo $this->donneesFormulaire->geth($field->getName()."_$i")?"OUI":"NON" ?>
										<br/>
								<?php endforeach;?>
							<?php else: ?>
								<?php echo $this->donneesFormulaire->geth($field->getName())?"OUI":"NON" ?>
							<?php endif; ?>
						<?php elseif($field->getType() == 'file') : ?>
							<?php 
							if ($this->donneesFormulaire->get($field->getName())):
									foreach($this->donneesFormulaire->get($field->getName()) as $num => $fileName ): ?>
										<a href='<?php echo $recuperation_fichier_url ?>&field=<?php echo $field->getName()?>&num=<?php echo $num ?>'><?php echo $fileName ?></a>
										<br/>	
								<?php endforeach;?>
							<?php endif;?>
						<?php elseif($field->getType() == 'select') : ?>
							<?php if ($field->getProperties('depend') && $this->donneesFormulaire->get($field->getProperties('depend'))) : ?>
								<?php foreach($this->donneesFormulaire->get($field->getProperties('depend')) as $i => $file) :  ?> 
											<?php echo $file ?> :
											<?php  
											$select = $field->getSelect();
											if (isset($select[$this->donneesFormulaire->geth($field->getName()."_$i")])) {
												echo $select[$this->donneesFormulaire->geth($field->getName()."_$i")];
											}
											?>
											<br/>
									<?php endforeach;?>
								<?php else: ?>
								<?php 
								$select = $field->getSelect();
								if (isset($select[$this->donneesFormulaire->geth($field->getName())])) {
									echo $select[$this->donneesFormulaire->geth($field->getName())];
								}?>
								<?php endif ?>
						<?php elseif ($field->getType() == 'password') : ?>
							<?php 
								if ($field->getProperties('may_be_null') && (! $this->donneesFormulaire->geth($field->getName()))){
									echo "(aucun)";
								} else {
									echo "*********";
								}
							?>
						<?php elseif ($field->getType() == 'date') : ?>
							<?php echo date_iso_to_fr($this->donneesFormulaire->geth($field->getName()))?>
						<?php elseif( $field->getType() == 'link') : ?>
							<a href='<?php echo SITE_BASE . $field->getProperties('script')?>?id_e=<?php echo $id_e ?>'><?php echo $field->getProperties('link_name')?></a>
						<?php elseif( $field->getType() == 'url') : ?>
							<a target='_blank' href='<?php hecho($this->donneesFormulaire->geth($field->getName()))?>'><?php hecho($this->donneesFormulaire->geth($field->getName())) ?></a>
						
						<?php else:?>
							
							<?php echo $this->donneesFormulaire->geth($field->getName(),$field->getDefault())?>
						<?php endif;?>			
					</td>
				</tr>				
			<?php 
			endforeach; ?>
		</table>
	<?php	
	}


	
}