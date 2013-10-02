<?php
class ConnexionControler extends PastellControler {
	
	public function verifConnected(){
		if (! $this->Authentification->isConnected()){
			$this->redirect("/connexion/connexion.php");
		}
	}
	
	public function casAuthentication(){
		$recuperateur = new Recuperateur($_GET);
		$id_ce = $recuperateur->getInt('id_ce');
		$casAuthentication = $this->ConnecteurFactory->getConnecteurById($id_ce);
		$login = $casAuthentication->authenticate(SITE_BASE."/connexion/cas.php?id_ce=$id_ce");
		$this->LastMessage->setLastMessage("Authentification avec le login : $login");
		$this->redirect("/connecteur/edition.php?id_ce=$id_ce");
	}
	
	public function connexionAction(){
		
		$authentificationConnecteur = $this->ConnecteurFactory->getGlobalConnecteur("authentification");
		
		if ($authentificationConnecteur){
			$login = $authentificationConnecteur->authenticate(SITE_BASE."/connexion/connexion.php");
			if (!$login){
				$this->LastError->setLastError("Le serveur CAS n'a pas donné de login");
				$this->redirect("/connexion/cas-error.php");
			}
			$id_u = $this->UtilisateurListe->getUtilisateurByLogin($login);
			if (!$id_u){
				$this->LastError->setLastError("Votre login cas est inconnu sur Pastell ($login) ");
				$this->redirect("/connexion/cas-error.php");
			}
			$infoUtilisateur = $this->Utilisateur->getInfo($id_u);
			$this->Journal->setId($id_u);
			$nom = $infoUtilisateur['prenom']." ".$infoUtilisateur['nom'];
			$this->Journal->add(Journal::CONNEXION,$infoUtilisateur['id_e'],0,"Connecté","$nom s'est connecté via CAS depuis l'adresse ".$_SERVER['REMOTE_ADDR']);
			
			$this->Authentification->connexion($login, $id_u);
			$this->redirect();
		}
		
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

	public function casErrorAction(){
		$this->page_title = "Erreur lors de l'authentification";
		$this->template_milieu = "CasError";
		$this->renderDefault();
	}
	
	public function logoutAction(){
		$this->Authentification->deconnexion();
			
		$authentificationConnecteur = $this->ConnecteurFactory->getGlobalConnecteur("authentification");
		
		if ($authentificationConnecteur){
			$authentificationConnecteur->logout();
		}
		$this->redirect("/connexion/connexion.php");
	}
	
}