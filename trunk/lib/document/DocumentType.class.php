<?php

require_once( PASTELL_PATH . "/lib/formulaire/Formulaire.class.php");

class DocumentType {
	
	const NOM = 'nom';
	const FORMULAIRE = 'formulaire';
	const ACTION = 'action';
	const PAGE_CONDITION = 'page-condition';
	const AFFICHE_ONE = 'affiche_one';
	const CONNECTEUR = 'connecteur';
	
	private $type;
	private $typeDefinition;
	
	public function __construct($type,array $typeDefinition){
		$this->type = $type; 
		$this->typeDefinition = $typeDefinition;
	}

	public function exists(){
		return  !! $this->typeDefinition; 
	}
	
	public function getName(){
		if (empty($this->typeDefinition[self::NOM])){
			return $this->type;
		}
		return $this->typeDefinition[self::NOM];
	}
	
	public function getConnecteur(){
		if (isset($this->typeDefinition[self::CONNECTEUR])){
			return $this->typeDefinition[self::CONNECTEUR];
		}
		return array();
	}
	
	public function getFormulaire(){	
		$formulaire =  new Formulaire($this->getFormulaireArray());
		if (isset( $this->typeDefinition[self::PAGE_CONDITION])){
			$formulaire->addPageCondition($this->typeDefinition[self::PAGE_CONDITION]);
		}
		if (! empty($this->typeDefinition[self::AFFICHE_ONE])){
			$formulaire->setAfficheOneTab();
		}
		return $formulaire;
	}
	
	private function getFormulaireArray(){
		if (empty($this->typeDefinition[self::FORMULAIRE])){
			return array();
		}
		return $this->typeDefinition[self::FORMULAIRE];
	}
	
	public function getAction(){
		if (empty($this->typeDefinition[self::ACTION])){
			return new Action();
		}
		return new Action((array) $this->typeDefinition[self::ACTION]);
	}
	
	public function getTabAction(){
		if (empty($this->typeDefinition[self::ACTION])){
			return array();
		}
		return $this->typeDefinition[self::ACTION];
	}
	
	
	
	
}