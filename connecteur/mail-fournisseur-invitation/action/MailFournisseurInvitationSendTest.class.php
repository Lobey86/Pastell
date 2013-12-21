<?php
class MailFournisseurInvitationSendTest extends ActionExecutor {
	
	/**
	 * @return MailFournisseurInvitation
	 */
	public function getMyConnecteur(){
		return parent::getMyConnecteur();
	}
	
	public function go(){
		$tmp_file = $this->objectInstancier->TmpFile->create();
		
		$connecteurProperties = $this->getConnecteurProperties();
		$from = $connecteurProperties->get('from');
		
		$donneesFormulaire = $this->getDonneesFormulaireFactory()->getNonPersistingDonneesFormulaire($tmp_file);
		$donneesFormulaire->setData("email", $from);
		$donneesFormulaire->setData("raison_sociale", "--Test raison sociale fournisseur--");

		$entiteInfo = $this->getEntite()->getInfo();
		
		$connecteur = $this->getMyConnecteur();
		$connecteur->send($donneesFormulaire,$entiteInfo);
		$this->setLastMessage("Message envoyé à $from");
		$this->objectInstancier->TmpFile->delete($tmp_file);
		return true;
	}
}