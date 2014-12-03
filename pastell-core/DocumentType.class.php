<?php
//Gère le contenu d'un fichier definition.yml d'un module
class DocumentType {
	
	const NOM = 'nom';
	const TYPE_FLUX = 'type';
	const FORMULAIRE = 'formulaire';
	const ACTION = 'action';
	const PAGE_CONDITION = 'page-condition';
	const AFFICHE_ONE = 'affiche_one';
	const CONNECTEUR = 'connecteur';
	const DESCRIPTION = 'description';
	
	const TYPE_FLUX_DEFAULT = 'Flux Généraux';
	
	private $module_id;
	private $module_definition;
	
	public function __construct($module_id,array $module_definition){
		$this->module_id = $module_id; 
		$this->module_definition = $module_definition;
	}

	public function exists(){
		return  !! $this->module_definition; 
	}
	
	public function getName(){
		if (empty($this->module_definition[self::NOM])){
			return $this->module_id;
		}
		return $this->module_definition[self::NOM];
	}
	
	public function getDescription(){
		if (empty($this->module_definition[self::DESCRIPTION])){
			return false;
		}
		return $this->module_definition[self::DESCRIPTION];
	}
	
	public function getType(){
		if (empty($this->module_definition[self::TYPE_FLUX])){
			return self::TYPE_FLUX_DEFAULT;
		}
		return $this->module_definition[self::TYPE_FLUX];
	}
	
	public function getConnecteur(){
		if (isset($this->module_definition[self::CONNECTEUR])){
			return $this->module_definition[self::CONNECTEUR];
		}
		return array();
	}
	
	public function getFormulaire(){	
		$formulaire =  new Formulaire($this->getFormulaireArray());
		if (isset( $this->module_definition[self::PAGE_CONDITION])){
			$formulaire->addPageCondition($this->module_definition[self::PAGE_CONDITION]);
		}
		if (! empty($this->module_definition[self::AFFICHE_ONE])){
			$formulaire->setAfficheOneTab();
		}
		return $formulaire;
	}
	
	private function getFormulaireArray(){
		if (empty($this->module_definition[self::FORMULAIRE])){
			return array();
		}
		return $this->module_definition[self::FORMULAIRE];
	}
	
	public function getAction(){
		if (empty($this->module_definition[self::ACTION])){
			return new Action();
		}
		return new Action((array) $this->module_definition[self::ACTION]);
	}
	
	public function getTabAction(){
		if (empty($this->module_definition[self::ACTION])){
			return array();
		}
		return $this->module_definition[self::ACTION];
	}
}