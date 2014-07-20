<?php

/**
 * Type de données de base de Pastell représentant un "formulaire" Pastell
 */
class Formulaire {

	private $formArray;
	private $tabSelected;
	private $pageCondition;
	private $afficheOneTab;
	private $origFormArray;
	
	public function __construct(array $formulaireDefinition){
		$this->formArray = $formulaireDefinition;	
		$this->origFormArray = $this->formArray;
		$this->setTabNumber(0);	
		$this->pageCondition = array();		
	}

	public function removeOnglet(array $onglet_to_remove){
		$this->formArray = $this->origFormArray;
		foreach($onglet_to_remove as $page){
			unset($this->formArray[$page]);
		}
	}
		
	public function setAfficheOneTab($afficheOneTab = true){
		$this->afficheOneTab = $afficheOneTab;
	}
	
	public function afficheOneTab(){
		return $this->afficheOneTab;
	}

	public function setTabNumber($tab_num){
		$array_keys = array_keys($this->formArray);
		if (isset($array_keys[$tab_num])){
			$this->tabSelected =  $array_keys[$tab_num];
		}
	}
	
	public function getTab(){
		$result = array();
		foreach ($this->formArray as $name => $tab) {
			$result[] = $name;
		}
		return $result;
	}
	
	public function getTabName($tabNumber){
		$tabName =  array_keys($this->formArray);
		return $tabName[$tabNumber];
	}
	
	public function getNbPage(){
		return count($this->formArray);
	}
	
	public function getFields(){
		$fields = array();	
		if (! $this->tabSelected){
			return $fields;
		}
		foreach($this->formArray[$this->tabSelected] as $libelle => $properties){
			$fields[] = new Field($libelle,$properties);	
		}
		return $fields; 
	}
	
	public function hasRequiredField(){
		foreach ($this->getFields() as  $field){
			if ($field->isRequired()) {
				return true;
			}
		}
		return false;
	}
	
	public function getAllFieldsWithHiddenFields() {
		$fields = array();
		foreach ($this->origFormArray as $name => $tab) {
			foreach($tab as $libelle => $properties){
				$field = new Field($libelle,$properties);
				$fields[$field->getName()]  = $field;
			}
		}
		return $fields;
	}
	
	public function getAllFields(){
		$fields = array();
		foreach ($this->formArray as $name => $tab) {
			foreach($tab as $libelle => $properties){
				$field = new Field($libelle,$properties);
				$fields[$field->getName()]  = $field;	
			}
		}
		return $fields;
	}
	
	/**
	 *
	 * @return array:Field renvoie l'ensemble des champs affichable pour l'onglet sélectionner
	 */
	public function getAllDisplayFields(){
		if ($this->afficheOneTab){
			return $this->getAllFields();
		} else {
			return $this->getFields();
		}
	}
	
	public function getTitreField(){
		foreach($this->getAllFields() as $field){
			if ($field->isTitle()){
				return $field->getName();
			}
		}
		return false;
	}
	
	public function getField($fieldName){
		foreach ($this->formArray as $name => $tab) {
			foreach($tab as $libelle => $properties){
				if(Field::Canonicalize($libelle) == Field::Canonicalize($fieldName)){
					return new Field($libelle,$properties);	
				}
			}
		}
		return false;
	}
	
	public function getTabNumber($tab_name){
		$i = 0;
		foreach ($this->formArray as $name => $tab) {
			if ($name == $tab_name){
				return $i;
			}
			$i ++;
		}
		return false;
	}
	
	public function tabNumberExists($tab_number = 0){
		return count($this->formArray)>$tab_number;
	}
}