<?php
class TedetisEnvoie  extends ActionExecutor {

	public function go(){
		$tdT = $this->getConnecteur("TdT"); 
		$tdT->postActes($this->getDonneesFormulaire());		
		$this->addActionOK("Le document a été envoyé au contrôle de légalité");
		return true;			
	}
}