<?php 


class CASTestLogout extends ActionExecutor {
	
	public function go(){
		$cas = $this->getMyConnecteur();
		$login = $cas->logout(SITE_BASE);
		$this->setLastMessage("Déconnecté avec succès");
		return true;
	}
	
}