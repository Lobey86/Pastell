<?php
class EnvoyerMessageAdmin extends ActionExecutor {
	
	public function go(){
		
		$all_user = $this->objectInstancier->RoleUtilisateur->getAllUtilisateurWithDroit($this->id_e,"entite:edition");
		
		$info_utilisateur = $this->objectInstancier->Utilisateur->getInfo($this->id_u);
		$entite_info = $this->getEntite()->getInfo();
		
		$zenMail = $this->getZenMail();
		$zenMail->setEmetteur("Pastell",PLATEFORME_MAIL);
		$zenMail->setSujet("[Pastell] Message d'un utilisateur ");
		$info = array('id_e' => $this->id_e,'id_d'=>$this->id_d,
		);		
		$info = array_merge($info_utilisateur,$info);
		$info = array_merge($info,$entite_info);
		
		
		$zenMail->setContenu(PASTELL_PATH . "/mail/message_admin.php",$info);
		
		foreach($all_user as $user){
			$zenMail->setDestinataire($user['email']);
			$zenMail->send();
			$this->getJournal()->add(Journal::ENVOI_MAIL,$this->id_e,$this->id_d,$this->action,"Message admin envoyée à {$user['email']}");	
		}
		
		$message = "Le document a été envoyé";
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,'envoi', $message);
		$this->setLastMessage($message);
		return true;
	}
	
}