<?php
class ActionExecutorFactory {
	
	private $action_class_directory;
	private $module_path;
	private $connecteur_path;
	
	private $lastMessage;
	
	private $objectInstancier;
	
	
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
	
	private function executeOnDocumentThrow($id_d,$id_e,$id_u,$action_name,$id_destinataire = array(),$from_api = false){
		$infoDocument = $this->objectInstancier->Document->getInfo($id_d);
		$type = $infoDocument['type'];
		$documentType = $this->objectInstancier->DocumentTypeFactory->getDocumentType($type);
		
		/*$theAction = $documentType->getAction();		
		$action_class_name = $theAction->getActionClass($action_name);
		if (!$action_class_name){
			throw new Exception("L'action $action_name n'existe pas.");
		}
		
		$action_class_file = $this->findDocumentActionFile($infoDocument['type'],$action_class_name);
		*/
		
		$actionClass = $this->goInstance($documentType,$id_e,$id_u,$type,$id_destinataire,$action_name,$from_api);
		$actionClass->setDocumentId($id_d);
		
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
		
		$actionClass = $this->goInstance($documentType,
			$connecteur_entite_info['id_e'],$id_u,$connecteur_entite_info['id_connecteur'],
			array(),$action_name,false);

		$actionClass->setConnecteurId($id_ce);
			
		$result = $actionClass->go();		
		$this->lastMessage = $actionClass->getLastMessage();		
		return $result;		
		
	}

	public function displayChoiceAction($id_e,$id_u,$id_d,$action_class_name,$from_api){
		//TODO
		/*$infoDocument = $this->objectInstancier->Document->getInfo($id_d);
		$action_class_file = $this->findClassFile($infoDocument['type'],$action_class_name);
		require_once($action_class_file);		
		$actionClass = new $action_class_name($this->objectInstancier,
		$id_e,$id_u,$infoDocument['type'],$id_destinataire,$action_name,$from_api);		
		$actionClass->display();*/		
	}
	
	
	private function goInstance($documentType,$id_e,$id_u,$type,$id_destinataire,$action_name,$from_api){
		$theAction = $documentType->getAction();		
		$action_class_name = $theAction->getActionClass($action_name);
		if (!$action_class_name){
			$this->lastMessage = "L'action $action_name n'existe pas.";
			return false;
		}
		
		$action_class_file = $this->findClassFile($type,$action_class_name);
		
		require_once($action_class_file);		
		$actionClass = new $action_class_name($this->objectInstancier,
		$type,$id_destinataire,$action_name,$from_api);
		$actionClass->setEntiteId($id_e);
		$actionClass->setUtilisateurId($id_u);
		
		return $actionClass;
		
	}
	

	private function findDocumentActionFile($flux, $action_class_name){
		
		$action_class_file = "{$this->module_path}/$flux/action/$action_class_name.class.php";
		if (file_exists($action_class_file)){
			return $action_class_file;
		}
					
		$action_class_file = "{$this->action_class_directory}/$action_class_name.class.php";
		if (file_exists($action_class_file )){
			return $action_class_file;	
		}
		
		$find = glob("{$this->module_path}/*/action/$action_class_name.class.php");
		
		if (count($find) == 0){				
			throw new Exception( "Le fichier $action_class_name est manquant");
		}
		return $find[0];
	}
	
	
	private function findClassFile($id_connecteur, $action_class_name){
		
		$action_class_file = "{$this->connecteur_path}/$id_connecteur/action/$action_class_name.class.php";
		if (file_exists($action_class_file)){
			return $action_class_file;
		}
					
		$action_class_file = "{$this->action_class_directory}/$action_class_name.class.php";
		if (file_exists($action_class_file )){
			return $action_class_file;	
		}
		
		$find = glob("{$this->module_path}/*/action/$action_class_name.class.php");
		
		if (count($find) == 0){				
			throw new Exception( "Le fichier $action_class_name est manquant");
		}
		return $find[0];
	}
	
}