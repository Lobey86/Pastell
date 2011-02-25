<?php

require_once( PASTELL_PATH . "/lib/action/Action.class.php");
require_once( PASTELL_PATH . "/lib/formulaire/Formulaire.class.php");

class DocumentType {
	
	const NOM = 'nom';
	const FORMULAIRE = 'formulaire';
	const ACTION = 'action';
	const PAGE_CONDITION = 'page-condition';
	
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
		return $formulaire;
	}
	
	public function getAction(){
		return new Action($this->typeDefinition[self::ACTION]);
	}
	
	
}