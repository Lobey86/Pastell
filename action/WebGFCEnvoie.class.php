<?php

require_once( PASTELL_PATH . "/action/Envoyer.class.php");
require_once(PASTELL_PATH . "/lib/system/WebGFC.class.php");

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
		$webGFC = new WebGFC();
		$courierID = $webGFC->createCourrier($messageSousTypeId,$contact,$titre,$object,$fichier,$username);
		$this->setLastMessage($webGFC->getLastMessage());
		if (! $courierID){
			return false;
		}
		$formulaire->setData("webgfc_courrier_id", $courierID);
		
		$id_col = $this->destinataire[0]; 
		
		$this->getDocumentEntite()->addRole($this->id_d,$id_col,"lecteur");
		
		$entiteCollectivite = new Entite($this->getSQLQuery(),$id_col);
		$infoCollectivite = $entiteCollectivite->getInfo();
		$denomination_col = $infoCollectivite['denomination']; 			
		
		$infoEntite = $this->getEntite()->getInfo();
		$emmeteurName = $infoEntite['denomination'];
		
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,'envoi', "Le document a été envoyé  à $denomination_col");
		$this->getActionCreator()->addToEntite($id_col,"Le document a été envoyé par $emmeteurName");
		
		$this->getActionCreator()->addAction($id_col,0,'recu', "Le document a été reçu ");
		$this->getActionCreator()->addToEntite($this->id_e,"Le document a été reçu par $denomination_col");
		
		$this->getNotificationMail()->notify($id_col,$this->id_d, $this->action, $this->type,"Vous avez un nouveau message");		
			
		$this->setLastMessage($webGFC->getLastMessage());
		return true;
	}
	
}