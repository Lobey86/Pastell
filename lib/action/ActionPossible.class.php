<?php
require_once (PASTELL_PATH . "/ext/spyc.php");

require_once (PASTELL_PATH . "/lib/document/DocumentEntite.class.php");
require_once (PASTELL_PATH . "/lib/document/Document.class.php");
require_once (PASTELL_PATH . "/lib/action/DocumentAction.class.php");
require_once (PASTELL_PATH . "/lib/formulaire/Field.class.php");
require_once (PASTELL_PATH . "/lib/formulaire/DonneesFormulaire.class.php");
require_once( PASTELL_PATH . "/lib/entite/EntiteProperties.class.php");


class ActionPossible {
		
	private $workflow_folder_path;
	private $allAction;
	private $documentAction;
	private $action;
	
	private $lastBadRule;
	
	
	public function __construct(SQLQuery $sqlQuery,Action $action,DocumentAction $documentAction){
		$this->sqlQuery = $sqlQuery;
		$this->documentAction = $documentAction; 
		$this->action = $action;
	}
		
	
	public function getLastBadRule(){
		return $this->lastBadRule;
	}
	
	public function isActionPossible( $id_d,$action, $id_e,$id_u){
		$action_possible = array_keys($this->getActionPossible($id_d,$id_e,$id_u));
		
		return in_array($action,$action_possible);
	}
	
	public function isCreationPossible($id_e,$id_u){
		$this->id_e = $id_e;
		$this->id_u = $id_u;
		$this->id_d = 0;
		$a = $this->action->getActionRule(Action::CREATION);
		return $this->testActionPossible($a);
	}
	
	
	public function getActionPossible($id_d,$id_e,$id_u){
				
		
		$this->id_d = $id_d ;
		$this->id_e = $id_e ;
		$this->id_u = $id_u ;
		
	
		$possible = array();
		
		foreach($this->action->getAll() as $action_name => $action_properties){
			if ($this->testActionPossible($action_properties['rule'])){
				$possible[$action_name] = $action_properties['name'];
			}
		}
		return $possible;
		
	}
	
	
	private function testActionPossible($action){
		foreach($action as $ruleName => $ruleValue){
			if ( ! $this->verifRule($ruleName,$ruleValue) ){
					$this->lastBadRule = "$ruleName:$ruleValue ne correspond pas";
		
				return false;
			}
		}
		return true;
	}
	
	private function verifRule($ruleName,$ruleValue){
		if (is_array($ruleValue) && $ruleName != 'content' && $ruleName != 'properties'){
			foreach($ruleValue as $ruleElement){
				if ($this->verifRule($ruleName,$ruleElement)){					
					return true;
				}
			}
			
			return false;
		}
		switch($ruleName){
			
			case 'no-last-action' : return $this->verifLastAction(false); break;
			case 'no-action':  return $this->verifNoAction($ruleValue); break;
			case 'last-action' : return $this->verifLastAction($ruleValue); break;
			case 'role_id_e' : return $this->verifRoleEntite($ruleValue); break;
			case 'droit_id_u' : return $this->verifDroitUtilisateur($ruleValue); break;
			case 'content' : return $this->verifContent($ruleValue); break;
			case 'type_id_e': return $this->veriTypeEntite($ruleValue); break;
			case 'document_is_valide' : return $this->verifDocumentIsValide(); break;
			case 'properties': return $this->verifProperties($ruleValue); break;
			case 'automatique': return false;
		}
		throw new Exception("Règle d'action inconnue : $ruleName" );
	} 
	
	private function verifLastAction($value){
		
		$documentActionEntite = new DocumentActionEntite($this->sqlQuery);
		
		$action = $documentActionEntite->getLastAction($this->id_e,$this->id_d);
		return $action == $value;
	}
	
	private function verifNoAction($value){
				
		$documentActionEntite = new DocumentActionEntite($this->sqlQuery);
		
		$lesActions = $documentActionEntite->getAction($this->id_e,$this->id_d);
		foreach($lesActions as $action){
			if ($action['action'] == $value){
				return false;
			}
		}
		return true;
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
	
	private function verifDocumentIsValide(){
		$document =new Document($this->sqlQuery);
		$infoDoc = $document->getInfo($this->id_d);
		$documentType = new DocumentType(DOCUMENT_TYPE_PATH);
		$donneesFormulaire = new DonneesFormulaire(WORKSPACE_PATH  . "/". $this->id_d .".yml");
		$donneesFormulaire->setFormulaire($documentType->getFormulaire($infoDoc['type']));	
		return $donneesFormulaire->isValidable();	
	}
	
	private function verifField($fieldName,$fieldValue){
		
		$field = new Field($fieldName,"");
		$fieldName = $field->getName();
		$donneesFormulaire = new DonneesFormulaire(WORKSPACE_PATH  . "/". $this->id_d .".yml");
		return $donneesFormulaire->get($fieldName) == $fieldValue;
	}
	
	private function veriTypeEntite($type){
		$entite = new Entite($this->sqlQuery,$this->id_e);
		$info = $entite->getInfo();
		return ($info["type"] == $type);
	}
	
	private function verifProperties(array $properties){
		foreach($properties as $key => $value) {}
		$entiteProperties = new EntiteProperties($this->sqlQuery,$this->id_e);
		$v = $entiteProperties->getProperties(EntiteProperties::ALL_FLUX,$key);
		
		return $v == $value;
	}
	
}