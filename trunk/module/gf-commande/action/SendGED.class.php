<?php 

class SendGED extends ActionExecutor {
	
	
	public function go(){
		$ged = $this->getConnecteur("GED");
		$folder = $ged->getRootFolder();
		
		$folder_name = $this->getDonneesFormulaire()->get("objet");
    	$folder_name = $ged->getSanitizeFolderName($folder_name);
    	
		$ged->createFolder($folder,$folder_name,"Pastell - Flux commande ");
		
		$sub_folder = $folder ."/$folder_name";
		
		$ged->createFolder($sub_folder,"Devis","Pastell - Devis");
		$ged->createFolder($sub_folder,"Bon_de_commande","Pastell - Bon de commande");
		$ged->createFolder($sub_folder,"Facture","Pastell - Facture");
		
		
		$this->sendFile($sub_folder."/Devis",'devis',"Devis");
		
		$this->sendFile($sub_folder."/Bon_de_commande",'bon_de_commande',"Bon de commande");
		$this->sendFile($sub_folder."/Bon_de_commande",'signature_bon_de_commande',"Signature du bon de commande");
		$this->sendFile($sub_folder."/Bon_de_commande",'bon_de_commande_signe',"Bon de commande signé");
		
		$this->sendFile($sub_folder."/Facture",'facture',"Facture");
		$this->sendFile($sub_folder."/Facture",'facture_signe',"Signature de la facture");
		$this->sendFile($sub_folder."/Facture",'signature_facture',"Facture signée");
		
		
		
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