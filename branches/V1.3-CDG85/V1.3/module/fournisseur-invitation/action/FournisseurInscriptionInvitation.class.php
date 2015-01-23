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
		$url_inscription = SITE_BASE."/fournisseur/pre-inscription.php?id_e={$this->id_e}&id_d={$this->id_d}&s=%SECRET%";
		$mailFournisseurInvitation->send($this->getDonneesFormulaire(), $this->getEntite()->getInfo(),$url_inscription);
		$message = "Invitation envoyé à ".$this->getDonneesFormulaire()->get('email');
		$this->addActionOK($message);
		$this->setLastMessage($message);
		return true;
	}
	
}