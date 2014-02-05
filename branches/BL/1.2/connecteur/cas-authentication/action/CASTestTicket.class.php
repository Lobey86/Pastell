<?php 


class CASTestTicket extends ActionExecutor {
	
	public function go(){
		$cas = $this->getMyConnecteur();
		$login = $cas->authenticate(SITE_BASE."/connexion/cas-pastell.php?id_ce={$this->id_ce}");
		if (!$login){
			$this->setLastMessage("Aucune session en cours");
			return false;
		}
		$this->setLastMessage("Authentifié avec le login : $login");
		return true;
	}
	
}