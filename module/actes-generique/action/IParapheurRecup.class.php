<?php


class IParapheurRecup extends ActionExecutor {
	
	public function go(){		
		$signature = $this->getConnecteur('signature');

		$actes = $this->getDonneesFormulaire();
		
		$dossierID = $signature->getDossierID($actes->get('numero_de_lacte'),$actes->get('objet'));
		$result = false;
		$erreur = false;
		try {
			$result = $signature->getHistorique($dossierID);
			if (! $result){
				$erreur = "Problème avec le parapheur : " . $signature->getLastError();
			}
		} catch (Exception $e){
			$erreur = $e->getMessage();
		}		
		
		if (strstr($result,"[Archive]")){
			return $this->retrieveDossier();
		} else if (strstr($result,"[RejetVisa]") || strstr($result,"[RejetSignataire]")){
			$this->rejeteDossier($result);
			$signature->effacerDossierRejete($dossierID);
			$this->setLastMessage($result);
			return true;
		} 
		$nb_jour_max = $signature->getNbJourMaxInConnecteur();
		$lastAction = $this->getDocumentActionEntite()->getLastActionInfo($this->id_e,$this->id_d);
		$time_action = strtotime($lastAction['date']);
		if (time() - $time_action > $nb_jour_max * 86400){
			$erreur = "Aucune réponse disponible sur le parapheur depuis $nb_jour_max !";
			$this->getActionCreator()->addAction($this->id_e,$this->id_u,'erreur-verif-iparapheur',$erreur);		
			$this->notify('erreur-verif-iparapheur', $this->type,$erreur);
		}			
		
		if (! $erreur){
			$this->setLastMessage($result);
			return true;	
		}
		$this->setLastMessage($erreur);										
		return false;
					
	}
	
	public function rejeteDossier($result){		
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,'rejet-iparapheur',"Le document a été rejeté dans le parapheur : $result");
	}
	
	public function retrieveDossier(){
		
		$signature = $this->getConnecteur('signature');
		
		$actes = $this->getDonneesFormulaire();
		$dossierID = $signature->getDossierID($actes->get('numero_de_lacte'),$actes->get('objet'));
		
		$info = $signature->getSignature($dossierID);
		if (! $info ){
			$this->setLastMessage("La signature n'a pas pu être récupéré : " . $signature->getLastError());
			return false;
		}
		
		$actes->setData('has_signature',true);
		if ($info['signature']){
			$actes->addFileFromData('signature',"signature.zip",$info['signature']);
		}
		$actes->addFileFromData('document_signe',$info['nom_document'],$info['document']);
		
		$this->setLastMessage("La signature a été récupérée");
		$this->notify('recu-iparapheur', $this->type,"La signature a été récupérée");
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,'recu-iparapheur',"La signature a été récupérée sur parapheur électronique");			
		return true;
		
	} 
	
}