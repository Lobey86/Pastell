<?php
class OpenIDSynchroniserCompte extends ActionExecutor {

	public function go(){
		$openID = $this->getMyConnecteur();
		$account_list = $openID->listAccount();

		$utilisateur = $this->objectInstancier->Utilisateur;
		
		foreach($account_list as $account){
			$id_u = $this->objectInstancier->Utilisateur->getIdFromLogin($account['user_id']);
			
			//$userInfo = $openID->getUserInfo($account['user_id']);
			
			if ($id_u){
				continue;
			}
			
			$password = mt_rand();
			$id_u = $utilisateur->create($account['user_id'],$password,$account['user_name'],"");
			$utilisateur->validMailAuto($id_u);
			$utilisateur->setColBase($id_u,0);
			$this->objectInstancier->RoleUtilisateur->addRole($id_u,RoleUtilisateur::AUCUN_DROIT,0);
			$this->objectInstancier->Journal->add(Journal::MODIFICATION_UTILISATEUR,0,0,"Ajout",
					"Ajout de l'utilisateur {$account['user_name']} via OpenID");
			
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