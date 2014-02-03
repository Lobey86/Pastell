<?php


class TestConnexion extends ActionExecutor {
	
	public function go(){
		$stela = $this->getMyConnecteur();
		$result = $stela->testConnexion();
		$this->setLastMessage("La connexion est réussie : " . $result);
		return true;
	}
	
}