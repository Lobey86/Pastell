<?php

require_once( PASTELL_PATH . "/lib/system/IParapheur.class.php");

class IParapheurEnvoieHelios extends ActionExecutor {
	
	public function go(){
		$collectiviteProperties = $this->getCollectiviteProperties();
		
		$iParapheur = new IParapheur($collectiviteProperties);		
		$helios = $this->getDonneesFormulaire();
		
		$file_content = file_get_contents($helios->getFilePath('fichier_pes'));
		$finfo = new finfo(FILEINFO_MIME);
		$content_type = $finfo->file($helios->getFilePath('fichier_pes'),FILEINFO_MIME_TYPE);
		
		$visuel_pdf  = file_get_contents($helios->getFilePath('visuel_pdf'));
		
		$file_array = $helios->get('fichier_pes');
		$filename = $file_array[0];
		
		$dossierID = $iParapheur->getDossierID($helios->get('objet'),$filename);
		
		
		$result = $iParapheur->sendHeliosDocument($helios->get('iparapheur_type'),
											$helios->get('iparapheur_sous_type'),
											$dossierID,
											$file_content,
											$content_type,$visuel_pdf);				
		if (! $result){
			$this->setLastMessage("La connexion avec le iParapheur a échoué : " . $iParapheur->getLastError());
			return false;
		}
		
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,$this->action,"Le document a été envoyé au parapheur électronique");			
		
		$this->setLastMessage("Le document a été envoyé au parapheur électronique");
		return true;			
	}
	
}