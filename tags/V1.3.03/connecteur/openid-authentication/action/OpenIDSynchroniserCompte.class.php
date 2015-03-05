<?php
class OpenIDSynchroniserCompte extends ActionExecutor {

	public function go(){
		$openID = $this->getMyConnecteur();
		$account_list = $openID->listAccount();

		$utilisateur = $this->objectInstancier->Utilisateur;
		
		$create = array();
		foreach($account_list as $account){
			$id_u = $this->objectInstancier->Utilisateur->getIdFromLogin($account['user_id']);
			if ($id_u){
				continue;
			}
			
			$password = mt_rand();
			$id_u = $utilisateur->create($account['user_id'],$password,$account['user_email_address'],"");
			$utilisateur->validMailAuto($id_u);
			$utilisateur->setColBase($id_u,$this->id_e);
			$utilisateur->setNomPrenom($id_u,$account['user_name'],"");
			
			$this->objectInstancier->RoleUtilisateur->addRole($id_u,RoleUtilisateur::AUCUN_DROIT,$this->id_e);
			$this->getJournal()->add(Journal::MODIFICATION_UTILISATEUR,$this->id_e,0,"Ajout","Ajout de l'utilisateur {$account['user_name']} via OpenID");
			
			$create[] = $account['user_name'];
			
		}
		if ($create) {
			$message = "Comptes créés : <br/>".implode("<br/>",$create);
		} else {
			$message = "Aucun compte n'a été créé";
		}
		$this->setLastMessage($message);
		return true;
	}
}