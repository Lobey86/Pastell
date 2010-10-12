<?php

require_once( PASTELL_PATH . "/lib/action/Action.class.php");
require_once( PASTELL_PATH . "/lib/formulaire/Formulaire.class.php");

class DocumentType {
	
	private $type;
	private $typeDefinition;
	
	public function __construct($type,array $typeDefinition){
		$this->type = $type; 
		$this->typeDefinition = $typeDefinition;
	}

	public function getName(){
		return $this->typeDefinition['nom'];
	}
	
	public function getFormulaire(){
		return new Formulaire($this->typeDefinition['formulaire']);
	}
	
	public function getAction(){
		return new Action($this->typeDefinition['action']);
	}
}