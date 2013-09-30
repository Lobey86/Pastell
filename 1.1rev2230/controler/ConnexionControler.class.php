<?php
class ConnexionControler extends PastellControler {
	
	
	public function connexionAction(){
		$messageConnexion = $this->ConnecteurFactory->getGlobalConnecteur("message-connexion");
		
		if ($messageConnexion){
			$this->message_connexion = $messageConnexion->getMessage();
		} else {
			$this->message_connexion = false;
		}
		
		$this->page="connexion";
		$this->page_title="Connexion";
		$this->template_milieu = "ConnexionIndex";
		$this->renderDefault();
	}
	
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
	
	public function changementMdpAction(){
		$recuperateur = new Recuperateur($_GET);
		$this->mail_verif_password = $recuperateur->get('mail_verif');
		
		$this->page="oublie_identifiant";
		$this->page_title="Oublie des identifiants";
		$this->template_milieu = "ConnexionChangementMdp";
		$this->renderDefault();
	}
	
	public function noDroitAction(){
		$this->page_title="Pas de droit";
		$this->template_milieu = "ConnexionNoDroit";
		$this->renderDefault();
	}
	
}