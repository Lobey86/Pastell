<?php

class RechercheAvanceFormulaireHTML extends PastellControler {
	
	private $documentType;
	private $recuperateur;
	
	private $objectInstancier;
	
	public function __construct(ObjectInstancier $objectInstancier){
		parent::__construct($objectInstancier);
		$this->recuperateur = new Recuperateur($_GET);
		$this->documentType = $objectInstancier->DocumentTypeFactory->getFluxDocumentType($this->getParameter('type'));
	}
	
	private function getParameter($field_name){
		return $this->recuperateur->get($field_name);
	}
	
	public function display(){
		$champs_recherche_avance = $this->documentType->getChampsRechercheAvancee();
		
		?><table class="table table-striped"><?php 
		foreach($champs_recherche_avance as $field_name){ ?>
				<tr>
					<th class="w300"><?php hecho($this->getLibelle($field_name))?></th>
					<td>
						<?php $this->displayInput($field_name);?>
					</td>
				</tr>
			<?php 
		}
		?></table><?php 
	}
	
	private function displayInput($field_name){
		$found= true;
		switch($field_name){
			case 'type': $this->displayTypeDocument(); break;
			case 'id_e': $this->displayEntite(); break;
			case 'lastetat': $this->displayLastState(); break;
			case 'last_state_begin': $this->displayLastStateBegin(); break;
			case 'etatTransit': $this->displayEtatTransit(); break;
			case 'state_begin': $this->displayStateBegin(); break;
			case 'search': $this->displayInputText('search'); break;
			case 'tri': $this->displayTri(); break;
			default: $found = false; break;
		}
		if ($found){
			return;
		}
		$field = $this->documentType->getFormulaire()->getField($field_name);
	
		switch($field->getType()){
			case 'date': $this->displayDate($field_name); break;
			case 'select': $this->displaySelect($field_name); break;
			case 'externalData': $this->displayExternalData($field_name);break;
			default: $this->displayInputText($field_name); 
		}
	}
	
	
	private function displayExternalData($field_name){
		$id_e = $this->getParameter('id_e');
		$type = $this->getParameter('type');
		$select = $this->getParameter($field_name);
		
		$id_d = 0;
		$field = $this->documentType->getFormulaire()->getField($field_name);
		
		$action_name = $field->getProperties('choice-action');
		$all_choice = $this->ActionExecutorFactory->getChoiceForSearch($id_e,$this->getId_u(),$type,$action_name,$field_name);
		?>
		
		<select name='<?php hecho($field_name) ?>'>
			<option value=''></option>
			<?php foreach($all_choice as $key => $value): ?>
				<option <?php echo $key==$select?"selected='selected'":"";?> value='<?php hecho($key)?>'><?php hecho($value)?></option>
			<?php endforeach; ?>
		</select>
		<?php 
		
		
	}
	
	private function displayDate($field_name){
		$date = $this->getParameter($field_name);
		dateInput($field_name,$date); 
	}
	
	private function displaySelect($field_name){
		$select = $this->getParameter($field_name);
		$field = $this->documentType->getFormulaire()->getField($field_name);
		$possible_value = $field->getSelect();
		?>
		<select name='<?php hecho($field_name)?>'>
			<option value=''></option>
			<?php foreach($possible_value as $value): ?>
			<option value='<?php hecho($value)?>' <?php echo $value==$select?"selected='selected'":"";?>>
				<?php hecho($value) ?> 
			</option>
			<?php endforeach ; ?>
		</select>
		<?php 
	}
	
	private function displayTri(){ 
		$tri = $this->getParameter('tri');
		$sens_tri = $this->getParameter('sens_tri');
		$type = $this->getParameter('type');
		$documentType = $this->DocumentTypeFactory->getFluxDocumentType($type);
		$indexedFieldsList = $documentType->getFormulaire()->getIndexedFields();
		?>
			<select name='tri'>
				<?php 
					foreach(array('date_dernier_etat' => "Date de dernière modification",
									"titre" => 'Titre du document',
									"entite" => "Nom de l'entité",							)
						as $key => $libelle
					) :
				?>
				<option value='<?php echo $key?>' <?php echo $tri==$key?'selected="selected"':''?>><?php echo $libelle?></option>
				<?php endforeach;?>
				<?php if($type):?>
					<?php foreach($indexedFieldsList as $indexField => $indexLabel) : ?>
						<option value='<?php hecho($indexField)?>' <?php echo $tri==$indexField?'selected="selected"':''?>><?php hecho($indexLabel)?></option>
					<?php endforeach;?>
				<?php endif;?>
			</select>
			<select name='sens_tri'>
				<option value='DESC' <?php echo $sens_tri == 'DESC'?'selected="selected"':''?>>Descendant</option>
				<option value='ASC' <?php echo $sens_tri == 'ASC'?'selected="selected"':''?>>Ascendant</option>
			</select>
	
	<?php 
	}
	
	private function displayEtatTransit(){ 
		$allDroit = $this->RoleUtilisateur->getAllDroit($this->getId_u());
		$listeEtat = $this->DocumentTypeFactory->getActionByRole($allDroit);
		$etatTransit = $this->getParameter('etatTransit');
		?>
		<select name='etatTransit'>
			<option value=''>----</option>
				<?php foreach($listeEtat as $typeDocument => $allEtat): ?>
				<optgroup label="<?php hecho($typeDocument) ?>">
				<?php foreach($allEtat as $nameEtat => $libelle): ?>
				<option value='<?php echo $nameEtat ?>' <?php echo $etatTransit == $nameEtat?"selected='selected'":"";?>>
					<?php echo $libelle ?>
				</option>
					<?php endforeach ; ?>
				</optgroup>
			<?php endforeach ; ?>

		</select>
		<?php 
	}
	
	private function displayStateBegin(){
		$state_begin = $this->getParameter('state_begin');
		$state_end = $this->getParameter('state_end');
		?>
	Du: 	<?php dateInput('state_begin',$state_begin); ?>
				&nbsp;&nbsp;au : <?php dateInput('state_end',$state_end); ?> 
	<?php 
	}	
	
	private function displayLastStateBegin(){ 
		$last_state_begin = $this->getParameter('last_state_begin');
		$last_state_end = $this->getParameter('last_state_end');
		?>
	
		Du: 	<?php $this->dateInput('last_state_begin',$last_state_begin); ?>
			&nbsp;&nbsp;au : <?php $this->dateInput('last_state_end',$last_state_end); ?> 
 	<?php 
	}
	
	private function displayLastState() {
		$allDroit = $this->RoleUtilisateur->getAllDroit($this->getId_u());
		$listeEtat = $this->DocumentTypeFactory->getActionByRole($allDroit);
		$lastEtat = $this->getParameter('lastetat');
		?>
		<select name='lastetat'>
			<option value=''>N'importe quel état</option>
			<?php foreach($listeEtat as $typeDocument => $allEtat): ?>
				<optgroup label="<?php hecho($typeDocument) ?>">
				<?php foreach($allEtat as $nameEtat => $libelle): ?>
				<option value='<?php echo $nameEtat ?>' <?php echo $lastEtat == $nameEtat?"selected='selected'":"";?>>
					<?php echo $libelle ?>
				</option>
					<?php endforeach ; ?>
				</optgroup>
			<?php endforeach ; ?>
			
		</select>
		<?php 
	}
	
	private function displayInputText($field_name){ ?>
		<input type='text' name='<?php hecho($field_name)?>' value='<?php echo $this->getParameter($field_name) ?>'/>
		<?php 
	}
	
	private function displayTypeDocument(){
		$this->DocumentTypeHTML->displaySelect($this->getParameter('type'),$this->getAllModule()); 
	}
	
	private function displayEntite(){ 
		$arbre = $this->RoleUtilisateur->getArbreFille($this->getId_u(),"entite:lecture");
		$id_e = $this->getParameter('id_e');
		
	?>
	<select name='id_e'>
			<?php foreach($arbre as $entiteInfo): ?>
			<option value='<?php echo $entiteInfo['id_e']?>' <?php echo $entiteInfo['id_e'] == $id_e?"selected='selected'":"";?>>
				<?php for($i=0; $i<$entiteInfo['profondeur']; $i++){ echo "&nbsp&nbsp;";}?>
				|_<?php echo $entiteInfo['denomination']?> </option>
			<?php endforeach ; ?>
		</select>
	<?php 
	}
	
	
	private function getLibelle($field_name){
		$defaultLibelle = array(
								'tri'=>'Trier le résultat',
								'lastetat'=>'Dernier état',
								'last_state_begin' => 'Date de passage dans le dernier état',
								'etatTransit'=>"Passé par l'état",
								'state_begin'=>'Date de passage dans cet état',
								'search'=>'Dont le titre contient',
								'type'=>'Type de document',
								'id_e'=>'Collectivité');
		if (isset($defaultLibelle[$field_name])){
			return $defaultLibelle[$field_name];
		}

		$field = $this->documentType->getFormulaire()->getField($field_name);
		if ($field){
			return $field->getLibelle();
		} 
		return $field_name;
		
	}
		

	private function dateInput($name,$value=''){
		?>
		<input 	type='text' 	
			id='<?php echo $name?>' 
			name='<?php echo $name?>' 
			value='<?php echo $value?>' 
			class='date'
			/>
		<script type="text/javascript">
	   		 jQuery.datepicker.setDefaults(jQuery.datepicker.regional['fr']);
			$(function() {
				$("#<?php echo $name?>").datepicker( { dateFormat: 'dd/mm/yy' });
				
			});
		</script>
		<?php 
	}
		
	
}