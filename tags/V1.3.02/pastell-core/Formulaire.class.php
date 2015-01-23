<?php

/**
 * Un Formulaire Pastell est un ensemble de champs Pastell (class Field) rangé dans des onglets.
 * 
 */
class Formulaire {

	private $formArray;
	private $tabSelected;
	private $afficheOneTab;
	private $origFormArray;
	private $fieldsList;
	
	/**
	 * Permet la construction d'un objet de type Formulaire
	 * 
	 * @param array $formulaireDefinition Un formulaire sous la forme d'un tableau associatif issu d'un fichier de définition de flux (definition.yml,entite-properties.yml,global-properties.yml)
	 */
	public function __construct(array $formulaireDefinition){
		$this->formArray = $formulaireDefinition;	
		$this->origFormArray = $this->formArray;
		$this->setTabNumber(0);	
		$this->fieldsList = array();
	}
	
	/**
	 * 
	 * @return array liste des noms des onglets 
	 */
	public function getOngletList(){
		$result = array();
		foreach ($this->origFormArray as $name => $tab) {
			$result[] = $name;
		}
		return $result;
	}

	/**
	 * @return array:Field Ensemble des objets de type Field composant le formulaire
	 */
	public function getFieldsList() {
		$fields = array();
		foreach ($this->origFormArray as $name => $tab) {
			foreach($tab as $libelle => $properties){
				$field = new Field($libelle,$properties);
				$fields[$field->getName()]  = $field;
			}
		}
		return $fields;
	}
	
	
	/**
	 * @param string $ongletName Nom de l'onglet (identique) à celui présent dans le fichier de définition du flux
	 * @return array:Field Ensemble des objets de type Field composant le formulaire
	 */
	public function getFieldsForOnglet($ongletName){
		$fieldsList = array();
		if (empty($this->formArray[$ongletName])){
			return array();
		}
		foreach($this->formArray[$ongletName] as $fieldName => $fieldProperties){
			$fieldsList[] = $this->createField($fieldName, $fieldProperties);
		}
		return $fieldsList;
	}
	
	/**
	 * 
	 * @param array $ongletList Nom des onglets à récupérer
	 * @return array:Field Tableau de l'ensemble des objets de type Field des onglets sélectionnés.
	 */
	public function getFieldsForOngletList(array $ongletList) {
		$fieldsList = array();
		foreach($ongletList as $ongletName) {
			$fieldsList = array_merge($fieldsList,$this->getFieldsForOnglet($ongletName)); 
		}
		return $fieldsList;
	}
	
	private function createField($fieldName,$fieldProperties){
		if (empty($this->fieldsList[$fieldName])){
			$this->fieldsList[$fieldName] = new Field($fieldName, $fieldProperties); 
		}
		return $this->fieldsList[$fieldName];
	}
	
	/* Les méthodes suivantes doivent être dépréciées*/
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
	
	
	public function getIndexedFields(){
		$result = array();
		foreach ($this->getAllFields() as $fields){
			if ($fields->isIndexed()){
				$result[$fields->getName()] = $fields->getLibelle();
			}
		}
		return $result;
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
	
	public function getField($fieldName,$ongletName = false){
		if ($ongletName){
			$tab = $this->origFormArray[$ongletName];
			foreach($tab as $libelle => $properties){
				if(Field::Canonicalize($libelle) == Field::Canonicalize($fieldName)){
					return new Field($libelle,$properties);
				}
			}
		} else {
			foreach ($this->origFormArray as $name => $tab) {
				foreach($tab as $libelle => $properties){				
					if(Field::Canonicalize($libelle) == Field::Canonicalize($fieldName)){
						return new Field($libelle,$properties);	
					}
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