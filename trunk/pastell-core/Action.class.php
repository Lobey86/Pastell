<?php

//Représente un objet de type action dont les informations
//sont dans un fichier de definition d'un flux à la clé action
// (de premier niveau)
class Action {
	
	const ACTION_DISPLAY_NAME = "name";
	const ACTION_DO_DISPLAY_NAME= "name-action";
	const ACTION_RULE = "rule";
	const ACTION_SCRIPT = "action-script";
	const AUTO_SCRIPT = "auto-script";
	const ACTION_CLASS = "action-class";
	const ACTION_AUTOMATIQUE = "action-automatique";
	const ACTION_DESTINATAIRE = "action-selection";
	const WARNING = "warning";
	const NO_WORKFLOW = "no-workflow";
	const EDITABLE_CONTENT = "editable-content";
	const PAS_DANS_UN_LOT = "pas-dans-un-lot";
	
	const CREATION = "creation";
	const MODIFICATION = "modification";
	
	
	
	private $tabAction;
	
	public function __construct(array $tabAction = array()){
		$this->tabAction = $tabAction;
	}
	
	public function getAll(){
		return array_keys($this->tabAction);
	}
	
	public function getActionName($action_internal_name){
		$tabAction = $this->getActionArray($action_internal_name);
		if (! isset($tabAction[self::ACTION_DISPLAY_NAME])){
			
			if ($action_internal_name == 'fatal-error'){
				return "Erreur fatale";
			}
			
			return $action_internal_name;
		}
		return $tabAction[self::ACTION_DISPLAY_NAME];
	}
	
	public function getDoActionName($action_internal_name){
		$tabAction = $this->getActionArray($action_internal_name);
		if (! isset($tabAction[self::ACTION_DO_DISPLAY_NAME])){
			return $this->getActionName($action_internal_name);
		}
		return $tabAction[self::ACTION_DO_DISPLAY_NAME];
	}
	
	
	private function getActionArray($action_internal_name){
		if (! isset($this->tabAction[$action_internal_name])){
			return array();
		}
		return $this->tabAction[$action_internal_name];
	}
	
	public function getActionRule($action_internal_name){
		$tabAction = $this->getActionArray($action_internal_name);
		if (empty($tabAction[self::ACTION_RULE])){
			return array();
		}
		return $tabAction[self::ACTION_RULE];
	}
	
	public function getProperties($action,$properties){
		$tabAction = $this->getActionArray($action);
		if (! isset ($tabAction[$properties])){
			return false;
		}
		return $tabAction[$properties];
	}
	
	public function getActionScript($action_internal_name){
		$tabAction = $this->getActionArray($action_internal_name);
		if (! isset($tabAction[self::ACTION_SCRIPT])){
			throw new Exception("L'action $action_internal_name n'est associé à aucun script");
		}
		return $tabAction[self::ACTION_SCRIPT];
	}
	
	public function getActionClass($action_internal_name){
		$tabAction = $this->getActionArray($action_internal_name);
		if (! isset($tabAction[self::ACTION_CLASS])){
			return false;
		}
		return $tabAction[self::ACTION_CLASS];
	}
	
	public function getActionDestinataire($action_internal_name){
		return $this->getProperties($action_internal_name,self::ACTION_DESTINATAIRE);
	}
	
	
	public function getAutoAction(){
		$result = array();
		foreach($this->getAll() as $actionName){
			$autoClass = $this->getProperties($actionName,self::ACTION_AUTOMATIQUE);
			if ($autoClass){
				$result[$actionName] = $autoClass;
			}	
		}
		return $result;
	}
	
	public function getWarning($action_name){
		if ($action_name == ActionPossible::FATAL_ERROR_ACTION){
			return true;
		}
		return $this->getProperties($action_name,self::WARNING);
	}
	
	public function getEditableContent($action_name){
		return $this->getProperties($action_name,self::EDITABLE_CONTENT);
	}
	
	public function getWorkflowAction(){
		$result = array();
		foreach($this->getAll() as $actionName){
			$no_workflow = $this->getProperties($actionName,self::NO_WORKFLOW);
			if (! $no_workflow){
				$result[$actionName] = $this->getActionName($actionName);
			}	
		}
		return $result;
	}
	
	public function getActionAutomatique($action){
		return $this->getProperties($action,self::ACTION_AUTOMATIQUE);
	}
	
	public function getActionWithNotificationPossible(){
		$result = array();
		foreach($this->getWorkflowAction() as $id => $name){
			$result[] = array('id'=>$id,'action_name'=>$name);		
		}
		return $result;
	}
	
	public function isPasDansUnLot($action_name){
		return $this->getProperties($action_name,self::PAS_DANS_UN_LOT);
		
	}
	
}