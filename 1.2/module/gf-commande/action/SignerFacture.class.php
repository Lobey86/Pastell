<?php 

class SignerFacture extends ActionExecutor {
	
	public function go(){
		
		$signature = $this->getConnecteur('signature');
		
		$actes = $this->getDonneesFormulaire();
		
		$file_content = file_get_contents($actes->getFilePath('facture'));
		$finfo = new finfo(FILEINFO_MIME);
		$content_type = $finfo->file($actes->getFilePath('facture'),FILEINFO_MIME_TYPE);
		$dossierID = $actes->getFileName('facture');
		
		$result = $signature->sendDocument($actes->getWithDefault('iparapheur_type_facture'),
											$actes->getWithDefault('sous_type_parapheur_facture'),
											$dossierID,
											$file_content,
											$content_type);				
		if (! $result){
			$this->setLastMessage("La connexion avec le iParapheur a échoué : " . $signature->getLastError());
			return false;
		}
		
		$this->addActionOK("Le document a été envoyé au parapheur électronique");
		return true;			
	}
	
}