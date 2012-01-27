<?php

require_once( PASTELL_PATH . "/lib/system/IParapheur.class.php");
require_once( PASTELL_PATH . "/lib/action/ActionExecutor.class.php");

class IParapheurEnvoie extends ActionExecutor {
	
	public function go(){
		$collectiviteProperties = $this->getCollectiviteProperties();
		
		$iParapheur = new IParapheur($collectiviteProperties);		
		$actes = $this->getDonneesFormulaire();
		
		$file_content = file_get_contents($actes->getFilePath('arrete'));
		$finfo = new finfo(FILEINFO_MIME);
		$content_type = $finfo->file($actes->getFilePath('arrete'),FILEINFO_MIME_TYPE);

		$result = $iParapheur->sendDocument($actes->get('iparapheur_type'),
											$actes->get('iparapheur_sous_type'),
											$actes->get('numero_de_lacte'),
											$file_content,
											$content_type);				
		if (! $result){
			$this->setLastMessage("La connexion avec le iParapheur a �chou� : " . $iParapheur->getLastError());
			return false;
		}
		
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,$this->action,"Le document a �t� envoy� au parapheur �lectronique");			
		
		$this->setLastMessage("Le document a �t� envoy� au parapheur �lectronique");
		return true;			
	}
	
}