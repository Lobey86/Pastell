<?php


class IParapheurEnvoieHelios extends ActionExecutor {
	
	public function go(){
		$signature = $this->getConnecteur('signature');
		
		$helios = $this->getDonneesFormulaire();
		
		$file_content = file_get_contents($helios->getFilePath('fichier_pes'));
		$finfo = new finfo(FILEINFO_MIME);
		$content_type = $finfo->file($helios->getFilePath('fichier_pes'),FILEINFO_MIME_TYPE);

//        if (! $helios->get('visuel_pdf')){
//			throw new Exception("Le visuel PDF est obligatoire pour l'envoi à la signature");
//		}
//		
//		$visuel_pdf  = file_get_contents($helios->getFilePath('visuel_pdf'));
        
        //Ajout
        if (! $helios->get('visuel_pdf')){
            $donneesFormulaireConnecteur = $this->objectInstancier->ConnecteurFactory->getConnecteurConfigByType($this->id_e,$this->type,'signature');
            if($donneesFormulaireConnecteur) {
                $pdf_default = $donneesFormulaireConnecteur->get("visuel_pdf_default");
                if( $pdf_default ) {
                    $helios->setData('visuel_pdf', $pdf_default);
                    $visuel_pdf  = file_get_contents($donneesFormulaireConnecteur->getFilePath('visuel_pdf_default'));
                }
                else {
                    throw new Exception("Le visuel PDF est obligatoire pour l'envoi à la signature");
                }
            }
		}
        else {
            $visuel_pdf  = file_get_contents($helios->getFilePath('visuel_pdf'));            
        }
        // Fin ajout

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