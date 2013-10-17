<?php 

class LDAPTestRecupEntite extends ActionExecutor {

	public function go(){
		$ldap = $this->getMyConnecteur();
		$login = $this->objectInstancier->Authentification->getLogin();
		$entry = $ldap->getEntite($login);
		if (!$entry){
			throw new Exception("Aucune entité trouvé pour $login");
		}
		$this->setLastMessage("Entité trouvé : " . $entry);
		return true;
	}

}