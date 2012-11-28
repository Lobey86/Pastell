<?php

require_once( PASTELL_PATH . "/lib/action/Action.class.php");
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

	public function getName(){
		if (empty($this->typeDefinition[self::NOM])){
			return $this->type;
		}
		return $this->typeDefinition[self::NOM];
	}
	
	public function getFormulaire(){
		$formulaire =  new Formulaire($this->typeDefinition[self::FORMULAIRE]);
		if (isset( $this->typeDefinition[self::PAGE_CONDITION])){
			$formulaire->addPageCondition($this->typeDefinition[self::PAGE_CONDITION]);
		}
		if (! empty($this->typeDefinition[self::AFFICHE_ONE])){
			$formulaire->setAfficheOneTab();
		}
		return $formulaire;
	}
	
	public function getAction(){
		return new Action((array) $this->typeDefinition[self::ACTION]);
	}
	
	public function getTabAction(){
		return $this->typeDefinition[self::ACTION];
	}
	
	public function getConnecteur(){
		if (isset($this->typeDefinition[self::CONNECTEUR])){
			return $this->typeDefinition[self::CONNECTEUR];
		}
		return array();
	}
	
}