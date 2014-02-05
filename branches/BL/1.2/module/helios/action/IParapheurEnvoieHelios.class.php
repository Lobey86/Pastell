<?php


class IParapheurEnvoieHelios extends ActionExecutor {
	
	public function go(){
		$signature = $this->getConnecteur('signature');
		
		$helios = $this->getDonneesFormulaire();
		
		$file_content = file_get_contents($helios->getFilePath('fichier_pes'));
		$finfo = new finfo(FILEINFO_MIME);
		$content_type = $finfo->file($helios->getFilePath('fichier_pes'),FILEINFO_MIME_TYPE);
		
		$visuel_pdf  = file_get_contents($helios->getFilePath('visuel_pdf'));
		
		$file_array = $helios->get('fichier_pes');
		$filename = $file_array[0];
		
		$dossierID = $signature->getDossierID($helios->get('objet'),$filename);
		
		
		$result = $signature->sendHeliosDocument($helios->get('iparapheur_type'),
											$helios->get('iparapheur_sous_type'),
											$dossierID,
											$file_content,
											$content_type,$visuel_pdf);				
		if (! $result){
			$this->setLastMessage("La connexion avec le iParapheur a échoué : " . $signature->getLastError());
			return false;
		}
		
		$this->addActionOK("Le document a été envoyé au parapheur électronique");
		return true;			
	}
	
}