<?php

require_once( PASTELL_PATH . "/lib/action/ActionExecutor.class.php");

class AccuserReception extends ActionExecutor {

	public function go(){
		$documentEntite = new DocumentEntite($this->getSQLQuery());
		$id_ged = $documentEntite->getEntiteWithRole($this->id_d,"editeur");
		
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,$this->action, "Vous avez accusé réception de ce message");
		$this->getActionCreator()->addToEntite($id_ged,"Un accusé de réception a été recu pour le document");
		
		$infoDocument = $this->getDocument()->getInfo($this->id_d);
		
		$message = "Un accusé de réception a été recu pour le document  {$infoDocument['titre']}"; 
		$message .= "\n\nConsulter le détail du document : " . SITE_BASE . "document/detail.php?id_d={$this->id_d}&id_e={$this->id_e}";
	
		
		$this->getNotificationMail()->notify($id_ged,$this->id_d,$this->action, $this->type,$message);


		$this->setLastMessage("L'accusé de réception a été envoyé à l'émetteur du message");
		return true;
	}
}