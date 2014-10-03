<?php
class Refuser extends ActionExecutor {
	
	
	public function go(){
		$id_u = $this->getDonneesFormulaire()->get('id_u');
		$message = $this->getDonneesFormulaire()->get('message');
		
		$utilisateur_info = $this->objectInstancier->Utilisateur->getInfo($id_u);
		
		$zenMail = $this->getZenMail();
		$zenMail->setEmetteur("Pastell",PLATEFORME_MAIL);
		$zenMail->setDestinataire($utilisateur_info['email']);
		$zenMail->setSujet("Votre changement de mail a été rejeté");
		$info = array("message" => $message);
		$zenMail->setContenu(PASTELL_PATH . "/mail/changement-email-refus.php",$info);
		$zenMail->send();
		
		$this->addActionOK("Changement d'email rejeté");
		return true;
	}
	
}