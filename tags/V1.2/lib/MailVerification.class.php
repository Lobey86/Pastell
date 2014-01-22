<?php 

class MailVerification {
	
	public function __construct(ZenMail $zenMail){
		$this->zenMail = $zenMail;
	}
	
	public function send($infoFournisseur){
		$this->zenMail->setEmetteur("Pastell",PLATEFORME_MAIL);
		$this->zenMail->setDestinataire($infoFournisseur['email']);
		$this->zenMail->setSujet("Votre inscription sur Pastell");
		$this->zenMail->setContenu(PASTELL_PATH . "/mail/inscription.php",$infoFournisseur);
		$this->zenMail->send();
	}
	
}