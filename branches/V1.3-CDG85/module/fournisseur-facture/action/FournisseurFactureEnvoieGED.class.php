<?php
class FournisseurFactureEnvoieGED extends ActionExecutor {
	
	public function go(){
		$ged = $this->getConnecteur('GED');		
		$donneesFormulaire = $this->getDonneesFormulaire();
		
		$folder = $ged->getRootFolder();		
		$folder_name = $donneesFormulaire->get("objet");		
		$folder_name = $ged->getSanitizeFolderName($folder_name);
		
		$ged->createFolder($folder,$folder_name,"Pastell - Flux Facture Fournisseur");
		$sub_folder = $folder ."/$folder_name";
		
		$meta_data = $donneesFormulaire->getMetaData();
		$ged->addDocument("metadata.txt","Meta données de l'actes","plain/text",$meta_data,$sub_folder);
		
		$all_file = $donneesFormulaire->getAllFile();
		foreach($all_file as $field){
			$files = $donneesFormulaire->get($field);
			foreach($files as $num_file => $file_name){
				$description = $this->getFormulaire()->getField($field)->getLibelle();
				$content = $this->getDonneesFormulaire()->getFileContent($field,$num_file);
				$contentType =  $this->getDonneesFormulaire()->getContentType($field,$num_file);
				$ged->addDocument($file_name,$description,$contentType,$content,$sub_folder);
			}
		}
		
		$id_e_fournisseur = $this->getDocumentEntite()->getEntiteWithRole($this->id_d, 'editeur');
		
		$actionCreator = $this->getActionCreator();
		$actionCreator->addAction($this->id_e,$this->id_u,$this->action, "La facture a été envoyée dans le SI");
		$actionCreator->addToEntite($id_e_fournisseur,"La facture a été récupéré par le SI de la collectivite");		
		$this->getNotificationMail()->notify($id_e_fournisseur,$this->id_d,$this->action,$this->type, "La facture a été récupérée par le SI de la collectivite");
		
		$this->setLastMessage("La facture a été envoyée dans le SI");
		return true;
		
	}
	
	
}