<?php 

class GEDEnvoi extends ActionExecutor {
	
	public function go(){
		$ged = $this->getConnecteur("GED");
		
		$folder = $ged->getRootFolder();
		
		$folder_name = $this->getDonneesFormulaire()->get("objet");
		$folder_name = $ged->getSanitizeFolderName($folder_name);
				
		$ged->createFolder($folder,$folder_name,"Pastell - Flux Helios ");
		$sub_folder = $folder ."/$folder_name";
		
		foreach(array('fichier_pes','visuel_pdf','iparapheur_historique','fichier_pes_signe','document_signe',
				'fichier_reponse','ar_sae','reply_sae'
		) as $key){
			$this->sendFile($sub_folder,$key);
		}	
		
		$this->addActionOK("Document envoyé sur la GED");
		$actionName  = $this->getActionName();
		$this->setLastMessage("L'action $actionName a été executé sur le document");
		return true;
	}
	
	public function sendFile($folder,$key){
		$ged = $this->getConnecteur("GED");		
		
		if ($this->getFormulaire()->getField($key)){
			$description = $this->getFormulaire()->getField($key)->getLibelle();
		} else {
			$description = $key;
		}
		
		
		
		$content = $this->getDonneesFormulaire()->getFileContent($key);
		if (!$content){
			return;
		}
		
		$filename = $this->getDonneesFormulaire()->getFileName($key);
		$contentType =  $this->getDonneesFormulaire()->getContentType($key);
		return $ged->addDocument($filename,$description,$contentType,$content,$folder);
	}
}