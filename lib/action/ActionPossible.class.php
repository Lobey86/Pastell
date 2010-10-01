<?php
require_once (PASTELL_PATH . "/ext/spyc.php");

require_once (PASTELL_PATH . "/lib/document/DocumentEntite.class.php");
require_once (PASTELL_PATH . "/lib/document/Document.class.php");
require_once (PASTELL_PATH . "/lib/action/DocumentAction.class.php");
require_once (PASTELL_PATH . "/lib/formulaire/Field.class.php");
require_once (PASTELL_PATH . "/lib/formulaire/DonneesFormulaire.class.php");


class ActionPossible {
		
	private $workflow_folder_path;
	private $allAction;
	private $documentAction;
	
	
	public function __construct(SQLQuery $sqlQuery,DocumentAction $documentAction){
		$this->sqlQuery = $sqlQuery;
		$this->documentAction = $documentAction; 
		$this->setWorkflowFolderPath(dirname(__FILE__) . "/../../workflow");
	}
	
	public function setWorkflowFolderPath($workflow_folder_path){
		$this->workflow_folder_path = $workflow_folder_path;
	}
	
	public function isActionPossible( $id_d,$action, $id_e,$id_u){
		$action_possible = $this->getActionPossible($id_d,$id_e,$id_u);
		return in_array($action,$action_possible);
	}
	
	public function getActionPossible($id_d,$id_e,$id_u){
		
		$this->id_d = $id_d ;
		$this->id_e = $id_e ;
		$this->id_u = $id_u ;
		
		$document = new Document($this->sqlQuery);
		$infoDoc = $document->getInfo($id_d);
		$type = $infoDoc['type'];
		
		$this->loadWorkflow($type);
		
		$possible = array();
		
		foreach($this->allAction as $action_name => $action_rules){
			if ($this->testActionPossible($action_rules)){
				$possible[] = $action_name;
			}
		}
		
		return $possible;
		
	}
	
	private function loadWorkflow($type){
		
		$file = $this->workflow_folder_path . "/" . $type . ".yml";
		if ( ! file_exists($file)){
			return array();
		}
		$this->allAction = Spyc::YAMLLoad($file);
		foreach($this->allAction as $name => $action){
			$this->allAction = $action;
			return;
		}
	}
	
	private function testActionPossible($action){
		foreach($action as $ruleName => $ruleValue){
			if ( ! $this->verifRule($ruleName,$ruleValue) ){
				return false;
			}
		}
		return true;
	}
	
	private function verifRule($ruleName,$ruleValue){
		if (is_array($ruleValue) && $ruleName != 'content'){
			foreach($ruleValue as $ruleElement){
				if ($this->verifRule($ruleName,$ruleElement)){
					return true;
				}
			}
			return false;
		}
		
		switch($ruleName){
			case 'last-action' : return $this->verifLastAction($ruleValue); break;
			case 'role_id_e' : return $this->verifRoleEntite($ruleValue); break;
			case 'droit_id_u' : return $this->verifDroitUtilisateur($ruleValue); break;
			case 'content' : return $this->verifContent($ruleValue); break;
		}
		throw new Exception("Règle d'action inconnue");
	}
	
	private function verifLastAction($value){
		$action = $this->documentAction->getLastAction();
		return $action == $value;
	}
	
	private function verifRoleEntite($value){
		$documentEntite = new DocumentEntite($this->sqlQuery);
		return $documentEntite->hasRole($this->id_d,$this->id_e,$value);
		
	}
	
	private function verifDroitUtilisateur($value){
		$roleUtilisateur = new RoleUtilisateur($this->sqlQuery,new RoleDroit());
		return $roleUtilisateur->hasDroit($this->id_u,$value,$this->id_e);
	}
	
	private function verifContent($value){
		foreach($value as $fieldName => $fieldValue){
			if ($this->verifField($fieldName,$fieldValue)){
				return true;
			}
		}
		return false;
	}
	
	private function verifField($fieldName,$fieldValue){
		
		$field = new Field($fieldName,"");
		$fieldName = $field->getName();
		$donneesFormulaire = new DonneesFormulaire(WORKSPACE_PATH  . "/". $this->id_d .".yml");
		return $donneesFormulaire->get($fieldName) == $fieldValue;
	}
	
}