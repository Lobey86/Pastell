<?php
class ConnexionControler extends PastellControler {
	
	public function oublieIdentifiantAction(){
		
		$config = false;
		try {
			$config = $this->ConnecteurFactory->getGlobalConnecteurConfig('message-oublie-identifiant');
		} catch(Exception $e){}
		
	
		$this->config = $config;
		
		$this->page="oublie_identifiant";
		$this->page_title = "Oublie des identifiants";
		$this->template_milieu = "ConnexionOublieIdentifiant";
		$this->renderDefault();
	}
	
}