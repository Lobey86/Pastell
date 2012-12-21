<?php
class ActionExecutorFactory {
	
	private $action_class_directory;
	private $module_path;
	private $connecteur_path;
	private $objectInstancier;
	
	private $lastMessage;
	
	public function __construct($action_class_directory, $module_path, $connecteur_path,ObjectInstancier $objectInstancier){
		$this->action_class_directory = $action_class_directory;
		$this->module_path = $module_path;	
		$this->objectInstancier = $objectInstancier;
		$this->connecteur_path = $connecteur_path;
	}
	
	public function getLastMessage(){
		return $this->lastMessage;
	}
	
	public function executeOnConnecteur($id_ce,$id_u,$action_name){
		try {
			return $this->executeOnConnecteurThrow($id_ce,$id_u,$action_name);
		} catch(Exception $e){
			$this->lastMessage = $e->getMessage();
			return false;	
		}
	}

	public function executeOnDocument($id_e,$id_u,$id_d,$action_name,$id_destinataire=array(),$from_api = false){
		try {		
			return $this->executeOnDocumentThrow($id_d, $id_e, $id_u,$action_name,$id_destinataire,$from_api);
		} catch (Exception $e){
			$this->lastMessage = $e->getMessage();
			return false;	
		}	
	}
	
	public function displayChoice($id_e,$id_u,$id_d,$action_name,$from_api,$field,$page = 0){
		
		$infoDocument = $this->objectInstancier->Document->getInfo($id_d);
		$documentType = $this->objectInstancier->DocumentTypeFactory->getDocumentType($infoDocument['type']);
		
		$action_class_name = $this->getActionClassName($documentType, $action_name);		
		
		$this->loadDocumentActionFile($infoDocument['type'],$action_class_name);
		$actionClass = $this->getInstance($action_class_name,$id_e,$id_u,$action_name);
		$actionClass->setDocumentId($infoDocument['type'], $id_d);
		$actionClass->setFromAPI($from_api);
		$actionClass->field = $field;
		$actionClass->page = $page;
		
		
		if ($from_api){
			$result = $actionClass->displayAPI();
		} else {
			$result = $actionClass->display();
		}		
	}
	
	public function isChoiceEnabled($id_e,$id_u,$id_d,$action_name){
		$infoDocument = $this->objectInstancier->Document->getInfo($id_d);
		$documentType = $this->objectInstancier->DocumentTypeFactory->getDocumentType($infoDocument['type']);
		
		$action_class_name = $this->getActionClassName($documentType, $action_name);		
		
		$this->loadDocumentActionFile($infoDocument['type'],$action_class_name);
		$actionClass = $this->getInstance($action_class_name,$id_e,$id_u,$action_name);
		$actionClass->setDocumentId($infoDocument['type'], $id_d);
		return $actionClass->isEnabled();
	}
	
	
	public function displayChoiceOnConnecteur($id_ce,$id_u,$action_name,$field){
		$connecteur_entite_info = $this->objectInstancier->ConnecteurEntiteSQL->getInfo($id_ce);
		if ($connecteur_entite_info['id_e']){				
			$documentType = $this->objectInstancier->documentTypeFactory->getEntiteDocumentType($connecteur_entite_info['id_connecteur']);
		} else {
			$documentType = $this->objectInstancier->documentTypeFactory->getGlobalDocumentType($connecteur_entite_info['id_connecteur']);
		}
		
		$action_class_name = $this->getActionClassName($documentType, $action_name);
		$action_class_file = $this->loadConnecteurActionFile($connecteur_entite_info['id_connecteur'],$action_class_name);
		
		$actionClass = $this->getInstance($action_class_name,$connecteur_entite_info['id_e'],$id_u,$action_name);
		$actionClass->setConnecteurId($connecteur_entite_info['id_connecteur'], $id_ce);
		$actionClass->setField($field);
		$result = $actionClass->display();		
		$this->lastMessage = $actionClass->getLastMessage();		
		return $result;		
	}
	
	
	
	public function goChoice($id_e,$id_u,$id_d,$action_name,$from_api,$field,$page = 0){
		$infoDocument = $this->objectInstancier->Document->getInfo($id_d);
		$documentType = $this->objectInstancier->DocumentTypeFactory->getDocumentType($infoDocument['type']);
		
		$action_class_name = $this->getActionClassName($documentType, $action_name);		
		$action_class_file = $this->loadDocumentActionFile($infoDocument['type'],$action_class_name);
		
		$actionClass = $this->getInstance($action_class_name,$id_e,$id_u,$action_name);
		$actionClass->setDocumentId($infoDocument['type'], $id_d);
		$actionClass->setFromAPI($from_api);
		$actionClass->field = $field;
		$actionClass->page = $page;

		$result = $actionClass->go();
		if ($from_api){
			$result['result'] = "ok";
			$this->objectInstancier->JSONoutput->display($result);
		} else {
			$actionClass->redirectToFormulaire();
		}
	}
	
	public function goChoiceOnConnecteur($id_ce,$id_u,$action_name,$field){
			$connecteur_entite_info = $this->objectInstancier->ConnecteurEntiteSQL->getInfo($id_ce);
		if ($connecteur_entite_info['id_e']){				
			$documentType = $this->objectInstancier->documentTypeFactory->getEntiteDocumentType($connecteur_entite_info['id_connecteur']);
		} else {
			$documentType = $this->objectInstancier->documentTypeFactory->getGlobalDocumentType($connecteur_entite_info['id_connecteur']);
		}
		
		$action_class_name = $this->getActionClassName($documentType, $action_name);
		$action_class_file = $this->loadConnecteurActionFile($connecteur_entite_info['id_connecteur'],$action_class_name);
		
		$actionClass = $this->getInstance($action_class_name,$connecteur_entite_info['id_e'],$id_u,$action_name);
		$actionClass->setConnecteurId($connecteur_entite_info['id_connecteur'], $id_ce);
		$actionClass->setField($field);			
		$result = $actionClass->go();		
		$actionClass->redirectToConnecteurFormulaire();
	}
	
	
	private function executeOnDocumentThrow($id_d,$id_e,$id_u,$action_name,$id_destinataire,$from_api){
		$infoDocument = $this->objectInstancier->Document->getInfo($id_d);
		$documentType = $this->objectInstancier->DocumentTypeFactory->getDocumentType($infoDocument['type']);
		
		$action_class_name = $this->getActionClassName($documentType, $action_name);		
		$action_class_file = $this->loadDocumentActionFile($infoDocument['type'],$action_class_name);
		
		$actionClass = $this->getInstance($action_class_name,$id_e,$id_u,$action_name);
		$actionClass->setDocumentId($infoDocument['type'], $id_d);
		$actionClass->setDestinataireId($id_destinataire);
		$actionClass->setFromAPI($from_api);
		$result = $actionClass->go();		
		$this->lastMessage = $actionClass->getLastMessage();		
		return $result;						
	}
	
	private function executeOnConnecteurThrow($id_ce,$id_u,$action_name){
		$connecteur_entite_info = $this->objectInstancier->ConnecteurEntiteSQL->getInfo($id_ce);
		if ($connecteur_entite_info['id_e']){				
			$documentType = $this->objectInstancier->documentTypeFactory->getEntiteDocumentType($connecteur_entite_info['id_connecteur']);
		} else {
			$documentType = $this->objectInstancier->documentTypeFactory->getGlobalDocumentType($connecteur_entite_info['id_connecteur']);
		}
		
		$action_class_name = $this->getActionClassName($documentType, $action_name);
		$action_class_file = $this->loadConnecteurActionFile($connecteur_entite_info['id_connecteur'],$action_class_name);
		
		$actionClass = $this->getInstance($action_class_name,$connecteur_entite_info['id_e'],$id_u,$action_name);
		$actionClass->setConnecteurId($connecteur_entite_info['id_connecteur'], $id_ce);			
		$result = $actionClass->go();		
		$this->lastMessage = $actionClass->getLastMessage();		
		return $result;		
		
	}
	
	private function getActionClassName(DocumentType $documentType,$action_name){
		$theAction = $documentType->getAction();		
		$action_class_name = $theAction->getActionClass($action_name);
		if (!$action_class_name){
			throw new Exception("L'action $action_name n'existe pas.");
		}
		return $action_class_name;
	}
	
	private function getInstance($action_class_name,$id_e,$id_u,$action_name){
		$actionClass = $this->objectInstancier->newInstance($action_class_name);
		$actionClass->setEntiteId($id_e);
		$actionClass->setUtilisateurId($id_u);
		$actionClass->setAction($action_name);
		return $actionClass;
	}

	private function loadDocumentActionFile($flux, $action_class_name){
		$action_class_file = "{$this->module_path}/$flux/action/$action_class_name.class.php";
		if (file_exists($action_class_file)){
			require_once($action_class_file);
		} else {
			$this->loadActionFile($action_class_name);
		}
	}
	
	private function loadConnecteurActionFile($id_connecteur, $action_class_name){
		$action_class_file = "{$this->connecteur_path}/$id_connecteur/action/$action_class_name.class.php";
		if (file_exists($action_class_file)){
			require_once($action_class_file);
		} else {
			$this->loadActionFile($action_class_name);
		}
	}
	
	private function loadActionFile($action_class_name){
		$action_class_file = "{$this->action_class_directory}/$action_class_name.class.php";
		if (file_exists($action_class_file )){
			return require_once($action_class_file);
		}		
		$find = glob("{$this->module_path}/*/action/$action_class_name.class.php");		
		if (count($find) == 0){				
			throw new Exception( "Le fichier $action_class_name est manquant");
		}
		require_once($find[0]);
	}
}