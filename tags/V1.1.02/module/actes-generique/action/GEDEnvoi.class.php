<?php 

class GEDEnvoi extends ActionExecutor {
	
	public function go(){
		$ged = $this->getConnecteur("GED");
		
		$folder = $ged->getRootFolder();
		
		$folder_name = $this->getDonneesFormulaire()->get("objet");
		$folder_name = strtr($folder_name," /", "__");
		
		$ged->createFolder($folder,$folder_name,"Pastell - Flux Actes");
		$sub_folder = $folder ."/$folder_name";
		
		foreach(array('Arreté','Autre document attaché'
		) as $key){
			$this->sendFile($sub_folder,$key);
		}	
		
		
		$this->setLastMessage("Document envoyé en GED");
		
		$actionName  = $this->getActionName();
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,$this->action,"Document envoyé sur la GED");
		$this->setLastMessage("L'action $actionName a été executé sur le document");
		return true;
	}
	
	public function sendFile($folder,$key){
		$ged = $this->getConnecteur("GED");		
		$description = $this->getFormulaire()->getField($key)->getLibelle();
		$content = $this->getDonneesFormulaire()->getFileContent($key);
		if (!$content){
			return;
		}
		$filename = $this->getDonneesFormulaire()->getFileName($key);
		$contentType =  $this->getDonneesFormulaire()->getContentType($key);
		return $ged->addDocument($filename,$description,$contentType,$content,$folder);
	}
}