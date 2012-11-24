<?php
require_once (PASTELL_PATH . "/lib/formulaire/Field.class.php");

class ActionPossible {
	private $lastBadRule;
	
	private $documentActionEntite;
	private $documentEntite;
	private $roleUtilisateur;
	private $entiteProperties;
	private $documentTypeFactory;
	private $document;
	private $entiteSQL;
	private $donneesFormulaireFactory;
	
	
	public function __construct(){		
		/*****/
		global $objectInstancier;
		/*****/
		
		$this->entiteProperties = $objectInstancier->EntitePropertiesSQL;
		$this->document = $objectInstancier->Document;
		$this->documentActionEntite = $objectInstancier->DocumentActionEntite;		
		$this->documentEntite = $objectInstancier->DocumentEntite;
		$this->roleUtilisateur = $objectInstancier->RoleUtilisateur;
		$this->entiteSQL = $objectInstancier->EntiteSQL;
		$this->documentTypeFactory = $objectInstancier->DocumentTypeFactory;
		$this->donneesFormulaireFactory = $objectInstancier->DonneesFormulaireFactory;
	}

	public function getLastBadRule(){
		return $this->lastBadRule;
	}
		
	public function isActionPossible($id_e,$id_u,$id_d,$action_name){
		$type_document = $this->getTypeDocument($id_e, $id_d);
		return $this->internIsActionPossible($id_e, $id_u, $id_d, $action_name,$type_document);
	}
	
	public function isCreationPossible($id_e,$id_u,$type_document){
		return $this->internIsActionPossible($id_e, $id_u, 0, Action::CREATION, $type_document);
	}

	public function getActionPossible($id_e,$id_u,$id_d){
		$type_document = $this->getTypeDocument($id_e, $id_d);
		$action = $this->getAction($type_document);
		$possible = array();
		
		foreach($action->getAll() as $action_name){
			if ($this->isActionPossible($id_e,$id_u,$id_d,$action_name)){
				$possible[] = $action_name;
			}
		}
		return $possible;
	}
	
	private function getTypeDocument($id_e,$id_d){
		$infoDocument = $this->document->getInfo($id_d);
		if (!$infoDocument){
			if ($id_e == $id_d){
				$infoDocument['type'] = 'collectivite-properties';
			}
			if ($id_e == 0){
				$infoDocument['type'] = 'entite0-properties';
			}
		}
		return $infoDocument['type'];
	}
	
	private function internIsActionPossible($id_e,$id_u,$id_d,$action_name,$type_document){
		$action = $this->getAction($type_document);
		$action_rule = $action->getActionRule($action_name);
	
		foreach($action_rule as $ruleName => $ruleValue){
			if ( ! $this->verifRule($id_e,$id_u,$id_d,$type_document,$ruleName,$ruleValue) ){
				$this->lastBadRule = "$ruleName:$ruleValue ne correspond pas";
				return false;
			}
		}
		return true;
	}
	
	private function getAction($document_type){
		return $this->documentTypeFactory->getDocumentType($document_type)->getAction();
	}
	
	private function verifRule($id_e,$id_u,$id_d,$type_document,$ruleName,$ruleValue){
		if (is_array($ruleValue) && ! in_array($ruleName,array('collectivite-properties','herited-properties','content','properties'))){
			foreach($ruleValue as $ruleElement){
				if ($this->verifRule($id_e,$id_u,$id_d,$type_document,$ruleName,$ruleElement)){					
					return true;
				}
			}
			
			return false;
		}
		switch($ruleName){			
			case 'no-last-action' : return $this->verifLastAction($id_e,$id_d,false); break;
			case 'last-action' : return $this->verifLastAction($id_e,$id_d,$ruleValue); break;
			case 'has-action' : return ! $this->verifNoAction($id_e,$id_d,$ruleValue); break;
			case 'no-action':  return $this->verifNoAction($id_e,$id_d,$ruleValue); break;
			case 'role_id_e' : return $this->verifRoleEntite($id_e,$id_d,$ruleValue); break;
			case 'droit_id_u' : return $this->verifDroitUtilisateur($id_e,$id_u,$ruleValue); break;
			case 'content' : return $this->verifContent($id_d,$type_document,$ruleValue); break;
			case 'type_id_e': return $this->veriTypeEntite($id_e,$ruleValue); break;
			case 'document_is_valide' : return $this->verifDocumentIsValide($id_d,$type_document); break;
			case 'properties': return $this->verifProperties($id_e,$ruleValue); break;
			case 'collectivite-properties': return $this->verifCollectiviteProperties($ruleValue); break;
			case 'herited-properties': return $this->verifHeritedProperties($id_e,$ruleValue); break;
			
			case 'automatique': return false;
		}
		throw new Exception("Règle d'action inconnue : $ruleName" );
	} 
	
	private function verifLastAction($id_e,$id_d,$value){				
		return $value == $this->documentActionEntite->getLastAction($id_e,$id_d);
	}
	
	private function verifNoAction($id_e,$id_d,$value){			
		$lesActions =  $this->documentActionEntite->getAction($id_e,$id_d);
		foreach($lesActions as $action){
			if ($action['action'] == $value){
				return false;
			}
		}
		return true;
	}
	
	private function verifRoleEntite($id_e,$id_d,$value){
		return $this->documentEntite->hasRole($id_d,$id_e,$value);
	}
	
	private function verifDroitUtilisateur($id_e,$id_u,$value){
		return $this->roleUtilisateur->hasDroit($id_u,$value,$id_e);
	}
	
	private function verifContent($id_d,$type,$value){
		foreach($value as $fieldName => $fieldValue){
			if (! $this->verifField($id_d,$type,$fieldName,$fieldValue)){
				return false;
			}
		}
		return true;
	}
	
	private function verifDocumentIsValide($id_d,$type){
		return $this->donneesFormulaireFactory->get($id_d,$type)->isValidable();
	}
	
	private function verifField($id_d,$type,$fieldName,$fieldValue){
		return $this->donneesFormulaireFactory->get($id_d,$type)->get(Field::Canonicalize($fieldName)) == $fieldValue;
	}
	
	private function veriTypeEntite($id_e,$type){
		$info = $this->entiteSQL->getInfo($id_e);
		return ($info["type"] == $type);
	}
	
	private function verifProperties($id_e,array $properties){
		foreach($properties as $key => $value) {}
		return $value == $this->entiteProperties->getProperties($id_e,EntitePropertiesSQL::ALL_FLUX,$key);		
	}
	
	private function verifCollectiviteProperties(array $properties){
		$collectiviteProperties = $this->donneesFormulaireFactory->get($id_e,'collectivite-properties');
		
		foreach($properties as $key => $value) {
			if ($collectiviteProperties->get($key) != $value){
				return false;
			}
			return true;
		}	
	}
	
	private function verifHeritedProperties($id_e,array $properties){
			$heritedProperties = $this->donneesFormulaireFactory->get($this->entiteSQL->getCollectiviteAncetre($id_e),'collectivite-properties');		
			foreach($properties as $key => $value) {
			if ($heritedProperties->get($key) != $value){
				return false;
			}
			return true;
		}	
	}
	
	
}