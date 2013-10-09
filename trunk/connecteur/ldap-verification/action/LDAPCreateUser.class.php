<?php 

class LDAPCreateUser extends ActionExecutor {
	
	public function go(){
		$ldap = $this->getMyConnecteur();
		$users = $ldap->getUserToCreate($this->objectInstancier->Utilisateur);
		$utilisateur = $this->objectInstancier->Utilisateur;
		
		foreach($users['todo'] as $user) {
			$password = mt_rand();
			$id_u = $utilisateur->create($user['login'],$password,$user['email'],"");
			$utilisateur->validMailAuto($id_u);
			$utilisateur->setNomPrenom($id_u,$user['nom'],$user['prenom']);
			
			$id_e = $this->objectInstancier->EntiteSQL->getIdByDenomination($user['entite']);
			$utilisateur->setColBase($id_u,$id_e);
			$this->objectInstancier->RoleUtilisateur->addRole($id_u,RoleUtilisateur::AUCUN_DROIT,$id_e);
			$this->objectInstancier->Journal->add(Journal::MODIFICATION_UTILISATEUR,$id_e,0,"Ajout",
					"Ajout de l'utilisateur {$user['login']} via LDAP");
		}
		$this->setLastMessage(count($users['todo']) . " utilisateurs ont été créés");
		return true;
	}
}