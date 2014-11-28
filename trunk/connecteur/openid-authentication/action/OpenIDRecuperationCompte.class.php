<?php
class OpenIDRecuperationCompte extends ActionExecutor {
	
	public function go(){
		$openID = $this->getMyConnecteur();
		$account_list = $openID->listAccount();
		
		$exist = array();
		$no_exist = array();
		
		foreach($account_list as $account){
			$id_u = $this->objectInstancier->Utilisateur->getIdFromLogin($account['user_id']); 
			if ($id_u){
				$exist[] = $account['user_name'];
		 	} else {
		 		$no_exist[] = $account['user_name'];
		 	}
		}
		
		$message = "Compte déjà existants : <br/>".implode("<br/>",$exist);
		$message .= "<br/>Compte à créer : <br/>".implode("<br/>",$no_exist);
		
		$this->setLastMessage($message);
		
		return true;
	}
}