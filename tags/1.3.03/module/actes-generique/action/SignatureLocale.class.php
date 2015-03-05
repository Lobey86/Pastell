<?php
class SignatureLocale extends ChoiceActionExecutor {
	
	public function go(){
		$recuperateur = new Recuperateur($_POST);
		$signature = $recuperateur->get("signature_1");
		if (! $signature){
			throw new Exception("Aucune signature n'a été trouvée");
		}
		$signature = base64_decode($signature);
		if (! $signature){
			throw new Exception("La signature n'est pas au bon format");
		}
		
		$actes = $this->getDonneesFormulaire();
		$actes->setData('signature_link', "La signature a été recupéré");
		$actes->addFileFromData('signature',"signature.pk7",$signature);
		
		$this->setLastMessage("La signature a été correctement récupéré");
		$this->redirect("/document/detail.php?id_e=".$this->id_e."&id_d=".$this->id_d."&page=".$this->page);
	}

	public function displayAPI(){
		throw new Exception("Cette fonctionnalité n'est pas disponible via l'API.");
	}
	
	public function display(){
		$this->libersign_url = $this->getConnecteurConfigByType('signature')->get("libersign_applet_url");
		$acte_file_path = $this->getDonneesFormulaire()->getFilePath("arrete");
		
		$sha1 = sha1_file($acte_file_path);
		
		$this->tab_included_files = array(array('id'=>$this->id_d,  'sha1'=> $sha1));
		
		
		$document_info = $this->getDocument()->getInfo($this->id_d);
		$this->info = $document_info;
		
		$type_name = $this->getDocumentTypeFactory()->getFluxDocumentType($this->type)->getName();
		
		$this->renderPage("Signature de l'acte - " .  $document_info['titre'] . " (".$type_name.")","SignatureLocale");
	}
	
}