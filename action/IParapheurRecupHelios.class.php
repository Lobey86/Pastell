<?php

require_once( PASTELL_PATH . "/lib/system/IParapheur.class.php");
require_once( PASTELL_PATH . "/lib/action/ActionExecutor.class.php");

class IParapheurRecupHelios extends ActionExecutor {
	
	public function go(){
		$collectiviteProperties = $this->getCollectiviteProperties();
		$iParapheur = new IParapheur($collectiviteProperties);		
		$helios = $this->getDonneesFormulaire();
		
		$dossierID = $iParapheur->getDossierID($helios->get('fichier_pes'),$helios->get('objet'));
		
		$result = $iParapheur->getHistorique($dossierID);				
		if (! $result){
			$this->setLastMessage("La connexion avec le iParapheur a échoué : " . $iParapheur->getLastError());
			return false;
		}
		if (strstr($result,"[Archive]")){
			return $this->retrieveDossier();
		}
		if (strstr($result,"[RejetVisa]") || strstr($result,"[RejetSignataire]")){
			$this->rejeteDossier($result);
			$iParapheur->effacerDossierRejete($dossierID);
		}
		$this->setLastMessage($result);
		return true;			
	}
	
	public function rejeteDossier($result){
		$collectiviteProperties = $this->getCollectiviteProperties();
		$iParapheur = new IParapheur($collectiviteProperties);
		$actes = $this->getDonneesFormulaire();
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,'rejet-iparapheur',"Le document a été rejeté dans le parapheur : $result");
	}
	
	public function retrieveDossier(){
		$collectiviteProperties = $this->getCollectiviteProperties();
		$iParapheur = new IParapheur($collectiviteProperties);		
		$helios = $this->getDonneesFormulaire();
		$dossierID = $iParapheur->getDossierID($helios->get('fichier_pes'),$helios->get('objet'));
		
		$info = $iParapheur->getSignature($dossierID);
		if (! $info ){
			$this->setLastMessage("La signature n'a pas pu être récupéré : " . $iParapheur->getLastError());
			return false;
		}
		
		$helios->setData('has_signature',true);
		if ($info['signature']){
			$helios->addFileFromData('signature',"signature.zip",$info['signature']);
		}
		$helios->addFileFromData('document_signe',$info['nom_document'],$info['document']);
		
		$this->setLastMessage("La signature a été récupérée");
		
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,'recu-iparapheur',"La signature a été récupérée sur parapheur électronique");			
		return true;
		
	} 
	
}