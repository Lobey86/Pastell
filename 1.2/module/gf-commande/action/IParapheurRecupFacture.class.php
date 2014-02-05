<?php


class IParapheurRecupFacture extends ActionExecutor {
	
	public function go(){
		
		$signature = $this->getConnecteur('signature');

		$actes = $this->getDonneesFormulaire();
		
		$dossierID = $actes->getFileName('facture');
				
		$result = $signature->getHistorique($dossierID);				
		if (! $result){
			$message = "Problème avec le parapheur : " . $signature->getLastError();
			$this->setLastMessage($message);
			$this->getActionCreator()->addAction($this->id_e,$this->id_u,'erreur-verif-iparapheur',$message);		
			$this->notify($this->action, $this->type,$message);													
			return false;
		}
		if (strstr($result,"[Archive]")){
			return $this->retrieveDossier();
		}
		if (strstr($result,"[RejetVisa]") || strstr($result,"[RejetSignataire]")){
			$this->rejeteDossier($result);
			$signature->effacerDossierRejete($dossierID);
		}
		$this->setLastMessage($result);
		return true;			
	}
	
	public function rejeteDossier($result){		
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,'rejet-iparapheur',"Le document a été rejeté dans le parapheur : $result");
	}
	
	public function retrieveDossier(){
		
		$signature = $this->getConnecteur('signature');
		
		$actes = $this->getDonneesFormulaire();
		$dossierID = $actes->getFileName('facture');
				
		$info = $signature->getSignature($dossierID);
		if (! $info ){
			$this->setLastMessage("La signature n'a pas pu être récupéré : " . $signature->getLastError());
			return false;
		}
		
		$actes->setData('facture_has_signature',true);
		if ($info['signature']){
			$actes->addFileFromData('signature_facture',"signature.zip",$info['signature']);
		}
		$actes->addFileFromData('facture_signe',$info['nom_document'],$info['document']);
		
		$this->setLastMessage("La signature a été récupérée");
		
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,'recu-iparapheur-facture',"La signature a été récupérée sur parapheur électronique");			
		return true;
		
	} 
	
}