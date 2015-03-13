<?php
class Accepter extends ActionExecutor {
	
	public function go(){
		$id_u = $this->getDonneesFormulaire()->get('id_u');
		$message = $this->getDonneesFormulaire()->get('message');
		$email = $this->getDonneesFormulaire()->get('email_demande');
		
		$this->objectInstancier->Utilisateur->setEmail($id_u,$email);
		
		$utilisateur_info = $this->objectInstancier->Utilisateur->getInfo($id_u);
		
		$zenMail = $this->getZenMail();
		$zenMail->setEmetteur("Pastell",PLATEFORME_MAIL);
		$zenMail->setDestinataire($utilisateur_info['email']);
		$zenMail->setSujet("Votre changement de mail a été accepté");
		$info = array("message" => $message);
		$zenMail->setContenu(PASTELL_PATH . "/mail/changement-email-accepter.php",$info);
		$zenMail->send();
		
		$this->addActionOK("Changement d'email accepté");
		return true;
	}
	
}