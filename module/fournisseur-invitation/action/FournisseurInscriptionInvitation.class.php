<?php
class FournisseurInscriptionInvitation extends ActionExecutor {
	
	/**
	 * @return MailFournisseurInvitation
	 */
	public function getMailFournisseurInvitation(){
		return $this->getConnecteur("mail-fournisseur-invitation");
	}
	
	public function go(){	
		$mailFournisseurInvitation = $this->getMailFournisseurInvitation();
		$mailFournisseurInvitation->send($this->getDonneesFormulaire(), $this->getEntite()->getInfo());
		$message = "Invitation envoyé à ".$this->getDonneesFormulaire()->get('email');
		$this->addActionOK($message);
		$this->setLastMessage($message);
		return true;
	}
	
}