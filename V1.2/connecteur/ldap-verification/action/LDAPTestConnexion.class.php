<?php 

class LDAPTestConnexion extends ActionExecutor {
	
	public function go(){
		$ldap = $this->getMyConnecteur();
		$ldap->getConnexion();
		$this->setLastMessage("La connexion est ok");
		return true;
	}
	
}