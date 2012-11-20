<?php

require_once (PASTELL_PATH . "/lib/document/Document.class.php");
require_once (PASTELL_PATH . "/lib/formulaire/Field.class.php");
require_once (PASTELL_PATH . "/lib/formulaire/DonneesFormulaire.class.php");
require_once( PASTELL_PATH . "/lib/entite/EntiteProperties.class.php");

require_once (PASTELL_PATH . "/lib/action/DocumentActionEntite.class.php");
require_once (PASTELL_PATH . "/lib/document/DocumentEntite.class.php");

class ActionPossible {

	private $id_e;
	private $id_u;
	private $action;
	
	private $id_d;
	private $lastBadRule;
	
	private $documentActionEntite;
	private $documentEntite;
	private $roleUtilisateur;
	private $donneesFormulaire;
	private $entite;
	private $entiteProperties;
	private $collectiviteProperties;
	private $heritedProperties;
	
	
	public function __construct(SQLQuery $sqlQuery,$id_e,$id_u,Action $action){
		
		$this->id_e = $id_e;
		$this->id_u = $id_u;	
		$this->action = $action;
		
		$this->setDocumentActionEntite(new DocumentActionEntite($sqlQuery));
		$this->setDocumentEntite(new DocumentEntite($sqlQuery));
		$this->setRoleUtilisateur( new RoleUtilisateur($sqlQuery) );
		$this->setEntite(new Entite($sqlQuery,$id_e));
		$this->setEntiteProperties(new EntiteProperties($sqlQuery,$id_e));
		
		global $donneesFormulaireFactory;
		$donneesFormulaire = $donneesFormulaireFactory->get($id_e,'collectivite-properties');
		
		$this->setCollectiviteProperties($donneesFormulaire);
		$this->setHeritedProperties($donneesFormulaire);
		
	}
	
	public function setDocumentActionEntite(DocumentActionEntite $documentActionEntite){
		$this->documentActionEntite = $documentActionEntite;
	}
	
	public function setDocumentEntite(DocumentEntite $documentEntite){
		$this->documentEntite = $documentEntite;
	}
	
	public function setRoleUtilisateur(RoleUtilisateur $roleUtilisateur){
		$this->roleUtilisateur = $roleUtilisateur;
	}
	
	public function setDonnesFormulaire(DonneesFormulaire $donneesFormulaire){
		$this->donneesFormulaire = $donneesFormulaire;
	}
	
	public function setEntite(Entite $entite){
		$this->entite = $entite;
	}
	
	public function setEntiteProperties(EntiteProperties $entiteProperties){
		$this->entiteProperties = $entiteProperties;
	}
	
	public function setCollectiviteProperties(DonneesFormulaire  $donnesFormulaire){
		$this->collectiviteProperties = $donnesFormulaire;
	}
	
	public function setHeritedProperties(DonneesFormulaire  $donnesFormulaire){
		$this->heritedProperties = $donnesFormulaire;
	}
	
	public function getLastBadRule(){
		return $this->lastBadRule;
	}
	
	public function isActionPossible($id_d,$action_name){
		
		$this->id_d = $id_d ;
		$action_rule = $this->action->getActionRule($action_name);
		foreach($action_rule as $ruleName => $ruleValue){
			if ( ! $this->verifRule($ruleName,$ruleValue) ){
				$this->lastBadRule = "$ruleName:$ruleValue ne correspond pas";
				return false;
			}
		}
		return true;
	}
	
	public function isCreationPossible(){
		return $this->isActionPossible(0,Action::CREATION);
	}
	
	public function getActionPossible($id_d){
		$possible = array();
		
		foreach($this->action->getAll() as $action_name){
			if ($this->isActionPossible($id_d,$action_name)){
				$possible[] = $action_name;
			}
		}
		return $possible;
	}
	
	private function verifRule($ruleName,$ruleValue){
		if (is_array($ruleValue) && ! in_array($ruleName,array('collectivite-properties','herited-properties','content','properties'))){
			foreach($ruleValue as $ruleElement){
				if ($this->verifRule($ruleName,$ruleElement)){					
					return true;
				}
			}
			
			return false;
		}
		switch($ruleName){			
			case 'no-last-action' : return $this->verifLastAction(false); break;
			case 'last-action' : return $this->verifLastAction($ruleValue); break;
			case 'has-action' : return ! $this->verifNoAction($ruleValue); break;
			case 'no-action':  return $this->verifNoAction($ruleValue); break;
			case 'role_id_e' : return $this->verifRoleEntite($ruleValue); break;
			case 'droit_id_u' : return $this->verifDroitUtilisateur($ruleValue); break;
			case 'content' : return $this->verifContent($ruleValue); break;
			case 'type_id_e': return $this->veriTypeEntite($ruleValue); break;
			case 'document_is_valide' : return $this->verifDocumentIsValide(); break;
			case 'properties': return $this->verifProperties($ruleValue); break;
			case 'collectivite-properties': return $this->verifCollectiviteProperties($ruleValue); break;
			case 'herited-properties': return $this->verifHeritedProperties($ruleValue); break;
			
			case 'automatique': return false;
		}
		throw new Exception("Règle d'action inconnue : $ruleName" );
	} 
	
	private function verifLastAction($value){				
		return $value == $this->documentActionEntite->getLastAction($this->id_e,$this->id_d);
	}
	
	private function verifNoAction($value){			
		$lesActions =  $this->documentActionEntite->getAction($this->id_e,$this->id_d);
		foreach($lesActions as $action){
			if ($action['action'] == $value){
				return false;
			}
		}
		return true;
	}
	
	private function verifRoleEntite($value){
		return $this->documentEntite->hasRole($this->id_d,$this->id_e,$value);
	}
	
	private function verifDroitUtilisateur($value){
		return $this->roleUtilisateur->hasDroit($this->id_u,$value,$this->id_e);
	}
	
	private function verifContent($value){
		assert('$this->donneesFormulaire');
		foreach($value as $fieldName => $fieldValue){
			if (! $this->verifField($fieldName,$fieldValue)){
				return false;
			}
		}
		return true;
	}
	
	private function verifDocumentIsValide(){
		assert('$this->donneesFormulaire');
		return $this->donneesFormulaire->isValidable();	
	}
	
	private function verifField($fieldName,$fieldValue){
		return $this->donneesFormulaire->get(Field::Canonicalize($fieldName)) == $fieldValue;
	}
	
	private function veriTypeEntite($type){
		$info = $this->entite->getInfo();
		return ($info["type"] == $type);
	}
	
	private function verifProperties(array $properties){
		foreach($properties as $key => $value) {}
		return $value == $this->entiteProperties->getProperties(EntiteProperties::ALL_FLUX,$key);		
	}
	
	public function verifCollectiviteProperties(array $properties){
		foreach($properties as $key => $value) {
			if ($this->collectiviteProperties->get($key) != $value){
				return false;
			}
			return true;
		}	
	}
	
	public function verifHeritedProperties(array $properties){
			foreach($properties as $key => $value) {
			if ($this->heritedProperties->get($key) != $value){
				return false;
			}
			return true;
		}	
	}
	
	
}