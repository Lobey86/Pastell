<?php
class ActionExecutorFactory {
	
	private $action_class_directory;
	private $module_path;
	
	private $lastMessage;
	
	private $objectInstancier;
	
	public function __construct($action_class_directory, $module_path, ObjectInstancier $objectInstancier){
		$this->action_class_directory = $action_class_directory;
		$this->module_path = $module_path;	
		$this->objectInstancier = $objectInstancier;
	}
	
	public function getLastMessage(){
		return $this->lastMessage;
	}
	
	public function executeOnGlobalProperties($id_u,$action_name){
		return $this->execute(0, 0, $id_u, 'entite0-properties', $action_name);
	}
	
	public function executeOnEntiteProperties($id_e,$id_u,$action_name){
		return $this->execute($id_e, $id_e, $id_u, 'collectivite-properties', $action_name);
	}
	
	public function executeOnDocument($id_e,$id_u,$id_d,$action_name,$id_destinataire=array()){
		$infoDocument = $this->objectInstancier->Document->getInfo($id_d);
		return $this->execute($id_d, $id_e, $id_u, $infoDocument['type'], $action_name);
	}
	
	//type = type de document !
	public function execute($id_d,$id_e,$id_u,$type,$action_name,$id_destinataire = array(),$from_api = false){
		
		$documentType = $this->objectInstancier->DocumentTypeFactory->getDocumentType($type);		
		$theAction = $documentType->getAction();		
		$action_class_name = $theAction->getActionClass($action_name);
		if (!$action_class_name){
			$this->lastMessage = "L'action $action_name n'existe pas.";
			return false;
		}
		
		$action_class_file = "{$this->module_path}/$type/action/$action_class_name.class.php";
		
		if (! file_exists($action_class_file)){		
			$action_class_file = "{$this->action_class_directory}/$action_class_name.class.php";
			if (! file_exists($action_class_file )){
				$this->lastMessage = "Le fichier $action_class_file est manquant";	
				return false;
			}
		}
		
		require_once($action_class_file);		
		$actionClass = new $action_class_name($this->objectInstancier,$id_d,$id_e,$id_u,$type,$id_destinataire,$action_name,$from_api);
		
		$result = $actionClass->go();		
		$this->lastMessage = $actionClass->getLastMessage();
		return $result;
	}
	
}