<?php

require_once( PASTELL_PATH . "/lib/system/IParapheur.class.php");
require_once( PASTELL_PATH . "/lib/action/ActionExecutor.class.php");

class IParapheurRecup extends ActionExecutor {
	
	public function go(){
		$collectiviteProperties = $this->getCollectiviteProperties();
		$iParapheur = new IParapheur($collectiviteProperties);		
		$actes = $this->getDonneesFormulaire();
		
		$num = $actes->get('numero_de_lacte');
		$result = $iParapheur->getHistorique($num);				
		if (! $result){
			$this->setLastMessage("La connexion avec le iParapheur a échoué : " . $iParapheur->getLastError());
			return false;
		}
		if (strstr($result,"[Archive]")){
			return $this->retrieveDossier();
		}
		$this->setLastMessage($result);
		return true;			
	}
	
	public function retrieveDossier(){
		$collectiviteProperties = $this->getCollectiviteProperties();
		$iParapheur = new IParapheur($collectiviteProperties);		
		$actes = $this->getDonneesFormulaire();
		$signature = $iParapheur->getSignature($actes->get('numero_de_lacte'));
		if (! $signature){
			$this->setLastMessage("La connexion avec le iParapheur a échoué : " . $iParapheur->getLastError());
			return false;
		}
		
		$actes->setData('has_signature',true);
		$actes->addFileFromData('signature',"signature.zip",$signature);
	
		$this->setLastMessage("La signature a été récupérée");
		
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,'recu-iparapheur',"La signature a été récupérée sur parapheur électronique");			
		return true;
		
	} 
	
}