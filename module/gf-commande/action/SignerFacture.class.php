<?php 

class SignerFacture extends ActionExecutor {
	
	public function go(){
		
		$signature = $this->getConnecteur('signature');
		
		$actes = $this->getDonneesFormulaire();
		
		$file_content = file_get_contents($actes->getFilePath('facture'));
		$finfo = new finfo(FILEINFO_MIME);
		$content_type = $finfo->file($actes->getFilePath('facture'),FILEINFO_MIME_TYPE);
		$dossierID = $actes->getFileName('facture');
		
		$result = $signature->sendDocument($actes->get('iparapheur_type_facture'),
											$actes->get('iparapheur_sous_type_facture'),
											$dossierID,
											$file_content,
											$content_type);				
		if (! $result){
			$this->setLastMessage("La connexion avec le iParapheur a �chou� : " . $signature->getLastError());
			return false;
		}
		
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,$this->action,"Le document a �t� envoy� au parapheur �lectronique");			
		
		$this->setLastMessage("Le document a �t� envoy� au parapheur �lectronique");
		return true;			
	}
	
}