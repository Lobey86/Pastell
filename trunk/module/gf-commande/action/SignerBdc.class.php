<?php 

class SignerBdc extends ActionExecutor {
	
	public function go(){
		
		$signature = $this->getConnecteur('signature');
		
		$actes = $this->getDonneesFormulaire();
		
		$file_content = file_get_contents($actes->getFilePath('bon_de_commande'));
		$finfo = new finfo(FILEINFO_MIME);
		$content_type = $finfo->file($actes->getFilePath('bon_de_commande'),FILEINFO_MIME_TYPE);
		$dossierID = $actes->getFileName('bon_de_commande');
		
		$result = $signature->sendDocument($actes->getWithDefault('iparapheur_type'),
											$actes->getWithDefault('sous_type_parapheur'),
											$dossierID,
											$file_content,
											$content_type);				
		if (! $result){
			$this->setLastMessage("La connexion avec le iParapheur a échoué : " . $signature->getLastError());
			return false;
		}
		
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,$this->action,"Le document a été envoyé au parapheur électronique");			
		
		$this->setLastMessage("Le document a été envoyé au parapheur électronique");
		return true;			
	}
	
}