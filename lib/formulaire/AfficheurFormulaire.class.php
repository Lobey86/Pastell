<?php
require_once("Formulaire.class.php");
require_once("DonneesFormulaire.class.php");

class AfficheurFormulaire {
	
	private $formulaire;
	private $donneesFormulaire;
	private $inject;
	
	public function __construct(Formulaire $formulaire, DonneesFormulaire $donneesFormulaire){
		$this->formulaire = $formulaire;
		$this->donneesFormulaire = $donneesFormulaire;
		$this->inject = array();	
	}
	
	public function injectHiddenField($name,$value){
		$this->inject[$name] = $value;
	}

	public function afficheTab($page,$url){ ?>
	
	<div id="bloc_onglet">
		<?php foreach ($this->formulaire->getTab() as $page_num => $name) : ?>
					<a href='<?php echo $url ?>&page=<?php echo $page_num?>' <?php echo ($page_num == $page)?'class="onglet_on"':'' ?>>
					<?php echo $name?>
					</a>
		<?php endforeach;?>
		</div>
		
	<?php 
	}
	
	public function affiche($page_number,$action_url,$recuperation_fichier_url  ){

					$this->formulaire->setTabNumber($page_number);
		
		?>
		<form action='<?php echo $action_url ?>' method='post' enctype="multipart/form-data">
			<input type='hidden' name='page' value='<?php echo $page_number?>' />
			<?php foreach($this->inject as $name => $value ) : ?>
			<input type='hidden' name='<?php echo $name ?>' value='<?php echo $value?>' />
			<?php endforeach;?>
			
			<table>
			<?php foreach ($this->formulaire->getFields() as $field) : ?>
				<tr>
					<th>
						<label for="<?php echo $field->getName() ?>"><?php echo $field->getLibelle() ?></label>
						<?php if ($field->isRequired()) : ?>*<?php endif;?>
						<?php if ($field->isMultiple()): ?>
							(plusieurs valeurs possibles)
						<?php endif;?>
					</th>
					<td> 
					<?php if ($field->getType() == 'checkbox') :?>
							<input type='checkbox' name='<?php echo $field->getName(); ?>' id='<?php echo $field->getName();?>' 
									<?php echo $this->donneesFormulaire->geth($field->getName())?"checked='checked'":'' ?>
									/>
					<?php elseif($field->getType() == 'textarea') : ?>
						<textarea rows='10' cols='40' id='<?php echo $field->getName();?>'  name='<?php echo $field->getName()?>'><?php echo $this->donneesFormulaire->get($field->getName())?></textarea>
					<?php elseif($field->getType() == 'file') : ?>
						<input type='file' id='<?php echo $field->getName();?>'  name='<?php echo $field->getName()?>' />
						<br/>
						<?php if ($this->donneesFormulaire->get($field->getName())):?>
								<a href='<?php echo $recuperation_fichier_url ?>&field=<?php echo $field->getName()?>'><?php echo $this->donneesFormulaire->geth($field->getName()) ?></a>
								&nbsp;&nbsp;<a href=''>supprimer</a>
						<?php endif;?>
						
					<?php elseif($field->getType() == 'select') : ?>
						<select name='<?php echo $field->getName()?>'>
							<option>...</option>
							<?php foreach($field->getSelect() as $value => $name) : ?>
								<option <?php 
									if ($this->donneesFormulaire->geth($field->getName()) == $value){
										echo "selected='selected'";
									}
								?> value='<?php echo $value ?>'><?php echo $name ?></option>
							<?php endforeach;?>
						</select>
					
					<?php else : ?>
						<input 	type='text' 	
								id='<?php echo $field->getName();?>' 
								name='<?php echo $field->getName(); ?>' 
								value='<?php echo $this->donneesFormulaire->geth($field->getName())?>' 
								size='40'/>
						<?php if($field->getType() == 'date') : ?>
						<link type="text/css" href="jquery/jquery-ui-1.8.2.custom.css" rel="stylesheet" />
						
						<script type='text/javascript' src='jquery/jquery-1.4.2.min.js'></script>
							<script type='text/javascript' src='jquery/jquery-ui-1.8.2.custom.min.js'></script>
						
						
							<script type="text/javascript">
								$(function() {
									$("#<?php echo $field->getName()?>").datepicker();
									
								});
							</script>
							
						<?php endif;?>
					<?php endif;?>						
					</td>
				</tr>				
			<?php endforeach; ?>
			</table>
		<?php if ($this->formulaire->hasRequiredField()): ?>
		* champs obligatoires.<br/>
		<?php endif;?>
			<input type='submit' name='enregistrer' value='Enregistrer' />
		</form>
	<?php }
	
	
	public function afficheStatic($page,$recuperation_fichier_url){
	
			$this->formulaire->setTabNumber($page);
		?>
		<table class='tab_01'>
			<?php foreach ($this->formulaire->getFields() as $i => $field) : ?>
				<tr class='<?php echo $i%2?'bg_class_gris':'bg_class_blanc'?>'>
					<td>
						<?php echo $field->getLibelle() ?>
					</td>
					<td>
						<?php if ($field->getType() == 'checkbox') :?>
							<?php echo $this->donneesFormulaire->geth($field->getName())?"OUI":"NON" ?>
						<?php elseif($field->getType() == 'file') : ?>
							<?php if ($this->donneesFormulaire->get($field->getName())):?>
								<a href='<?php echo $recuperation_fichier_url ?>&field=<?php echo $field->getName()?>'><?php echo $this->donneesFormulaire->geth($field->getName()) ?></a>
							<?php endif;?>
						<?php elseif($field->getType() == 'select') : ?>
							<?php 
								$select = $field->getSelect();
								if (isset($select[$this->donneesFormulaire->geth($field->getName())])) {
									echo $select[$this->donneesFormulaire->geth($field->getName())];
								}
							?>
						<?php else:?>
							<?php echo $this->donneesFormulaire->geth($field->getName())?>
						<?php endif;?>			
					</td>
				</tr>				
			<?php endforeach; ?>
		</table>
	<?php	
	}
	
}