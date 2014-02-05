<?php 

class TedetisSendReponsePref extends ActionExecutor {
	
	public function go(){
		$tdT = $this->getConnecteur("TdT"); 
		$id = $tdT->sendResponse( $this->getDonneesFormulaire());
		$message = "reponse envoyé à la préfecture";
		$this->addActionOK($message);
		$this->setLastMessage($message);
		return true;
	}
	
	
}
