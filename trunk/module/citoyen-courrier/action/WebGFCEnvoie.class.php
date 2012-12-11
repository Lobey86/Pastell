<?php


class WebGFCEnvoie extends ActionExecutor {
	
	public function go(){
		
		$formulaire = $this->getDonneesFormulaire();
		$messageSousTypeId = $formulaire->get("messageSousTypeId");
		$entite = $this->getEntite();
		$infoEntite = $entite->getInfo();
		$contact = $infoEntite['denomination'];
		$username = $infoEntite['denomination'];
		$titre = $formulaire->get("sujet");
		$object  = $formulaire->get("message");
		if ($formulaire->getFilePath("pj")){
			$fichier = file_get_contents($formulaire->getFilePath("pj"));
		} else {
			$fichier = "";
		}
		
		$webGFC = $this->getConnecteur('GFC');
		
		$courierID = $webGFC->createCourrier($messageSousTypeId,$contact,$titre,$object,$fichier,$username);
		$this->setLastMessage($webGFC->getLastMessage());
		if (! $courierID){
			return false;
		}
		$formulaire->setData("webgfc_courrier_id", $courierID);
		
		$id_col = $this->id_destinataire[0]; 
		
		$this->getDocumentEntite()->addRole($this->id_d,$id_col,"lecteur");
		
		$entiteCollectivite = new Entite($this->getSQLQuery(),$id_col);
		$infoCollectivite = $entiteCollectivite->getInfo();
		$denomination_col = $infoCollectivite['denomination']; 			
		
		$infoEntite = $this->getEntite()->getInfo();
		$emmeteurName = $infoEntite['denomination'];
		
		$actionCreator = $this->getActionCreator();
		
		$actionCreator->addAction($this->id_e,$this->id_u,'envoi', "Le document a été envoyé  à $denomination_col");
		$actionCreator->addToEntite($id_col,"Le document a été envoyé par $emmeteurName");
		
		$actionCreator->addAction($id_col,0,'recu', "Le document a été reçu ");
		$actionCreator->addToEntite($this->id_e,"Le document a été reçu par $denomination_col");
		
		$this->getNotificationMail()->notify($id_col,$this->id_d, $this->action, $this->type,"Vous avez un nouveau message");		
			
		$this->setLastMessage($webGFC->getLastMessage());
		return true;
	}
	
}