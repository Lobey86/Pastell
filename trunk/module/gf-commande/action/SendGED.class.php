<?php 

class SendGED extends ActionExecutor {
	
	
	public function go(){
		$ged = $this->getConnecteur("GED");
		
		$config = $this->getConnecteurConfigByType("GED");
		$folder = $config->get('ged_folder');
		
		$folder_name = $this->getDonneesFormulaire()->get("objet");
    	$folder_name = str_replace(" ", "_", $folder_name);
				
		$ged->createFolder($folder,$folder_name,"Pastell - Flux commande ");
		
		$sub_folder = $folder ."/$folder_name";
		
		
		$to_send = array('bon_de_commande'=>"Bon de commande",
						"devis"=>"Devis",
						"bon_de_commande_signe"=>"Signature du bon de commande",
						"facture"=>"Facture",
						"facture_signe" => "Signature de la facture"
		);
		foreach($to_send as $key => $description){
			$this->sendFile($sub_folder,$key,$description);
		}
		
		$this->setLastMessage("Document envoyé en GED");
		
		$actionName  = $this->getActionName();
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,$this->action,"Document envoyé sur la GED");
		$this->setLastMessage("L'action $actionName a été executé sur le document");
		return true;
	}
	
	public function sendFile($folder,$key,$description){
		$ged = $this->getConnecteur("GED");
		
		$content = $this->getDonneesFormulaire()->getFileContent($key);

		$filename = $this->getDonneesFormulaire()->getFileName($key);
		$contentType =  $this->getDonneesFormulaire()->getContentType($key);
		return $ged->addDocument($filename,$description,$contentType,$content,$folder);
		
	}
	
	
}