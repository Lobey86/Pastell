<?php
class TedetisEnvoie  extends ActionExecutor {

	public function go(){
		$tdT = $this->getConnecteur("TdT"); 
		$tdT->postActes($this->getDonneesFormulaire());		
		
		$tdtConfig = $this->getConnecteurConfigByType("TdT");
		if ($tdtConfig->get('authentication_for_teletransmisson')){
			$this->changeAction("document-transmis-tdt","Le document a été envoyé au TdT");
			$this->notify("document-transmis-tdt", $this->type,"Le document a été envoyé au TdT");
				
		} else {
			$this->addActionOK("Le document a été envoyé au contrôle de légalité");
			$this->notify($this->action, $this->type,"Le document a été envoyé au contrôle de légalité");
				
		}
		
		return true;
	}
}