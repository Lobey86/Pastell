<?php
require_once("Formulaire.class.php");
require_once("DonneesFormulaire.class.php");
require_once( PASTELL_PATH . "/lib/helper/date.php");

class AfficheurFormulaire {
	
	private $formulaire;
	private $donneesFormulaire;
	private $inject;
	private $onePage;
	
	public function __construct(Formulaire $formulaire, DonneesFormulaire $donneesFormulaire){
		$this->formulaire = $formulaire;
		$this->donneesFormulaire = $donneesFormulaire;
		$this->formulaire->addDonnesFormulaire($donneesFormulaire);
		$this->inject = array();	
	}
	
	public function injectHiddenField($name,$value){
		$this->inject[$name] = $value;
	}

	public function afficheTab($tab_selected,$page_url){ 
		if ($this->formulaire->afficheOneTab()){
			return;
		}
		?>
	
		<div id="bloc_onglet">
		<?php foreach ($this->formulaire->getTab() as $page_num => $name) : ?>
					<a href='<?php echo $page_url ?>&page=<?php echo $page_num?>' <?php echo ($page_num == $tab_selected)?'class="onglet_on"':'' ?>>
					<?php echo $name?>
					</a>
		<?php endforeach;?>
		</div>
		
	<?php 
	}
	
	public function afficheStaticTab($page){
		?>
		<div id="bloc_onglet">
		<?php foreach ($this->formulaire->getTab() as $page_num => $name) : ?>
					<a <?php echo ($page_num == $page)?'class="onglet_on"':'' ?>>
					<?php echo ($page_num + 1) . ". " . $name?>
					</a>
		<?php endforeach;?>
		</div>
		<?php 
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
		
		?>
		<form action='<?php echo $action_url ?>' method='post' enctype="multipart/form-data">
			<input type='hidden' name='page' value='<?php echo $page_number?>' />
			<?php foreach($this->inject as $name => $value ) : ?>
			<input type='hidden' name='<?php echo $name ?>' value='<?php echo $value?>' />
			<?php endforeach;?>
			
			<table>
			<?php foreach ($this->formulaire->getFields() as $field) :
				if ($field->getProperties('read-only') && $field->getType() == 'file'){
					continue;
				}
				if ($field->getProperties('no-show')){
					continue;
				}
			?>
				<tr>
					<th>
						<label for="<?php echo $field->getName() ?>"><?php echo $field->getLibelle() ?></label>
						<?php if ($field->isRequired()) : ?>*<?php endif;?>
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
									/><?php echo $file ?> 
									<br/>
							<?php endforeach;?>
						<?php else:?>
							<input type='checkbox' name='<?php echo $field->getName(); ?>' id='<?php echo $field->getName();?>' 
									<?php echo $this->donneesFormulaire->geth($field->getName())?"checked='checked'":'' ?>
									/>
						<?php endif; ?>
					<?php elseif($field->getType() == 'textarea') : ?>
						<textarea rows='10' cols='40' id='<?php echo $field->getName();?>'  name='<?php echo $field->getName()?>'><?php echo $this->donneesFormulaire->get($field->getName(),$field->getDefault())?></textarea>
					<?php elseif($field->getType() == 'file') :?>
						<?php if ( ( $field->isMultiple()) || (! $this->donneesFormulaire->get($field->getName()))) : ?>
							<input type='file' id='<?php echo $field->getName();?>'  name='<?php echo $field->getName()?>' />
						<?php endif; ?>
						<?php if ($field->isMultiple()): ?>
							<input class='input_normal send_button' type='submit' name='ajouter' value='Ajouter' />
						<?php endif;?>
						<?php if ( ( $field->isMultiple()) || (! $this->donneesFormulaire->get($field->getName()))) : ?>
						<br/>
						<?php endif;?>
					<?php 
					
						if ($this->donneesFormulaire->get($field->getName())) : 
						foreach($this->donneesFormulaire->get($field->getName()) as $num => $fileName ): ?>
								<a href='<?php echo $recuperation_fichier_url ?>&field=<?php echo $field->getName()?>&num=<?php echo $num ?>'><?php echo $fileName ?></a>
								&nbsp;&nbsp;<a href='<?php echo $suppression_fichier_url ?>&field=<?php echo $field->getName() ?>&num=<?php echo $num ?>'>supprimer</a>
							<br/>
						<?php endforeach; endif;?>
					<?php elseif(($field->getType() == 'select') && ! $field->getProperties('read-only')) : ?>
						<select name='<?php echo $field->getName()?>'>
							<option value=''>...</option>
							<?php foreach($field->getSelect() as $value => $name) : ?>
								<option <?php 
									if ($this->donneesFormulaire->geth($field->getName()) == $value){
										echo "selected='selected'";
									}
								?> value='<?php echo $value ?>'><?php echo $name ?></option>
							<?php endforeach;?>
						</select>
					<?php elseif ($field->getType() == 'externalData') :?>
						<?php if($id_ce) : ?>
							<a href='<?php echo  $externalDataURL ?>?id_ce=<?php echo $id_ce ?>&field=<?php echo $field->getName()?>'><?php echo $field->getProperties('link_name')?></a>
						<?php elseif($field->isEnabled($this->inject['id_e']) && isset($id_e)) :?>
							<a href='<?php echo  $externalDataURL ?>?id_e=<?php echo $id_e ?>&id_d=<?php echo $id_d ?>&page=<?php echo $page_number?>&field=<?php echo $field->getName()?>'><?php echo $field->getProperties('link_name')?></a>
						<?php else:?>
							non disponible
						<?php endif;?>
						<?php echo $this->donneesFormulaire->get($field->getName())?>
					<?php elseif ($field->getType() == 'password') : ?>
						<input 	type='password' 	
								id='<?php echo $field->getName();?>' 
								name='<?php echo $field->getName(); ?>' 
								value='' 
								size='16'
						/>
					<?php elseif( $field->getType() == 'link') : ?>
						<a href='<?php echo SITE_BASE . $field->getProperties('script')?>?id_e=<?php echo $id_e?>'><?php echo $field->getProperties('link_name')?></a>					
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
		* champs obligatoires.<br/>
		<?php endif;?>
		
		<?php if ($page_number > 0 ): ?>
				<input type='submit' name='precedent' value='« Précédent' class='send_button'/>
		<?php endif; ?>
		
			<input type='submit' name='enregistrer' value='Enregistrer' class='send_button' />
			
		<?php if ( ($this->formulaire->getNbPage() > 1) && ($this->formulaire->getNbPage() > $page_number + 1)): ?>
				<input type='submit' name='suivant' value='Suivant »' class='send_button' />
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
			?><div class="box_error">
					<p>
						 <?php  echo $this->donneesFormulaire->getLastError(); ?>
					</p>
						
				</div>
			
			<?php 
		}
		
			$this->formulaire->setTabNumber($page);
		?>
		<table class='tab_01'>
			<?php
			$i=0;
			foreach ($this->getFieldStatic() as $field) :
					if ($field->getType() == 'externalData' && $this->donneesFormulaire->geth($field->getName()) == '') {
						continue;
					} 
					if ($field->getProperties('no-show')){
						continue;
					}
			
			?>
				<tr class='<?php echo $i++%2?'bg_class_gris':'bg_class_blanc'?>'>
					<td>
						<?php echo $field->getLibelle() ?>
					</td>
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
							<?php 
								$select = $field->getSelect();
								if (isset($select[$this->donneesFormulaire->geth($field->getName())])) {
									echo $select[$this->donneesFormulaire->geth($field->getName())];
								}
							?>
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