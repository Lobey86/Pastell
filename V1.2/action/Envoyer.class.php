<?php

class Envoyer extends ActionExecutor {

	
	public function go(){
		
		if ( ! $this->id_destinataire ){
			$this->setLastMessage("Vous devez selectionner un destinataire");
			return false;
		}
		
		$infoEntite = $this->getEntite()->getInfo();
		$emmeteurName = $infoEntite['denomination'];
		
		
		foreach($this->id_destinataire  as $id_col) {
			
			$this->getDocumentEntite()->addRole($this->id_d,$id_col,"lecteur");
			
			$entiteCollectivite = new Entite($this->getSQLQuery(),$id_col);
			$infoCollectivite = $entiteCollectivite->getInfo();
			$denomination_col = $infoCollectivite['denomination']; 			
			
			$actionCreator = $this->getActionCreator();
			$actionCreator->addAction($this->id_e,$this->id_u,'envoi', "Le document a été envoyé  à $denomination_col");
			$actionCreator->addToEntite($id_col,"Le document a été envoyé par $emmeteurName");
			
			$actionCreator->addAction($id_col,0,'recu', "Le document a été reçu ");
			$actionCreator->addToEntite($this->id_e,"Le document a été reçu par $denomination_col");
			
			$message = $emmeteurName. " vous envoie un nouveau message.\n\n";
			$message.= "Pour le consulter : " . SITE_BASE . "document/detail.php?id_d={$this->id_d}&id_e=$id_col";
			
			$this->getNotificationMail()->notify($id_col,$this->id_d, $this->action, $this->type,$message);		
		}
		
		$this->setLastMessage("Le document a été envoyé au(x) entité(s) selectionnée(s)");
		return true;		
	}
}