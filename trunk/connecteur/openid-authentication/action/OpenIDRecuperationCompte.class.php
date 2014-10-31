<?php
class OpenIDRecuperationCompte extends ActionExecutor {
	
	public function go(){
		$openID = $this->getMyConnecteur();
		$account_list = $openID->listAccount();
		
		return true;
	}
}