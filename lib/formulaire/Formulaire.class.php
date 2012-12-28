<?php
require_once("Field.class.php");

class Formulaire {

	private $formArray;
	private $tabSelected;
	private $pageCondition;
	private $afficheOneTab;
	
	public function __construct(array $formulaireDefinition){
		$this->formArray = $formulaireDefinition;	
		$this->setTabNumber(0);	
		$this->pageCondition = array();		
	}

	public function delTab($page){
		unset($this->formArray[$page]);
	}
	
	public function addPageCondition(array $pageCondition){
		$this->pageCondition = $pageCondition;
	}
	
	public function addDonnesFormulaire(DonneesFormulaire $donnesFormulaire){
		$page_a_enlever = array();
		
		foreach($this->pageCondition as $page => $condition){
			foreach($condition as $field => $value){
				if ($donnesFormulaire->get($field) != $value){
					$page_a_enlever[$page] = true;
				}
			}	
		}
		foreach($page_a_enlever as $page => $nb ){
			unset($this->formArray[$page]);
		}
	}
	
	public function setAfficheOneTab(){
		$this->afficheOneTab = true;
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
				if($libelle == $fieldName){
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
	
	
	
}