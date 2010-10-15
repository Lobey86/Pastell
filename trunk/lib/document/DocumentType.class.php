<?php

require_once( PASTELL_PATH . "/lib/action/Action.class.php");
require_once( PASTELL_PATH . "/lib/formulaire/Formulaire.class.php");

class DocumentType {
	
	const NOM = 'nom';
	const FORMULAIRE = 'formulaire';
	const ACTION = 'action';
	
	private $type;
	private $typeDefinition;
	
	public function __construct($type,array $typeDefinition){
		$this->type = $type; 
		$this->typeDefinition = $typeDefinition;
	}

	public function getName(){
		return $this->typeDefinition[self::NOM];
	}
	
	public function getFormulaire(){
		return new Formulaire($this->typeDefinition[self::FORMULAIRE]);
	}
	
	public function getAction(){
		return new Action($this->typeDefinition[self::ACTION]);
	}
	
	
}