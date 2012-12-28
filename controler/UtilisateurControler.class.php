<?php
class UtilisateurControler extends PastellControler {
	
	public function modifPasswordAction(){
		$this->page_title = "Modification de votre mot de passe";
		$this->template_milieu = "UtilisateurModifPassword";
		$this->renderDefault();
	}
	
	public function modifEmailAction(){
		$this->utilisateur_info = $this->Utilisateur->getInfo($this->Authentification->getId()); 
		if ($this->utilisateur_info['id_e'] == 0){
			$this->LastError->setLastError("Les utilisateurs de l'entité racine ne peuvent pas utiliser cette procédure");
			$this->redirect("/utilisateur/moi.php");
		}
		$this->page_title = "Modification de votre email";
		$this->template_milieu = "UtilisateurModifEmail";
		$this->renderDefault();
	}
	
	public function modifEmailControlerAction(){
		$recuperateur = new Recuperateur($_POST);
		$password = $recuperateur->get('password');
		if ( ! $this->Utilisateur->verifPassword($this->Authentification->getId(),$password)){
			$this->LastError->setLastError("Le mot de passe est incorect.");
			$this->redirect("/utilisateur/modif-email.php");
		}
		$email = $recuperateur->get('email');
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
			$this->LastError->setLastError("L'email que vous avez saisi ne semble pas être valide");
			$this->redirect("/utilisateur/modif-email.php");
		}
		
		$utilisateur_info = $this->Utilisateur->getInfo($this->Authentification->getId()); 
		
		
		$password = $this->UtilisateurNewEmailSQL->add($this->Authentification->getId(),$email);
		
		$zenMail = $this->ZenMail;
		$zenMail->setEmmeteur("Pastell",PLATEFORME_MAIL);
		$zenMail->setDestinataire($email);
		$zenMail->setSujet("Changement de mail sur Pastell");
		$info = array("password" => $password);
		$zenMail->setContenu(PASTELL_PATH . "/mail/changement-email.php",$info);
		$zenMail->send();
		
		$this->Journal->add(Journal::MODIFICATION_UTILISATEUR,$utilisateur_info['id_e'],0,"change-email","Demande de changement d'email initiée {$utilisateur_info['email']} -> $email");
		
		
		$this->LastMessage->setLastMessage("Un email a été envoyé à votre nouvelle adresse. Merci de le consulter pour la suite de la procédure.");
		$this->redirect("/utilisateur/moi.php");
	}
	
	public function modifEmailConfirmAction(){
		$recuperateur = new Recuperateur($_GET);
		$password = $recuperateur->get('password');
		$info = $this->UtilisateurNewEmailSQL->confirm($password);
		if ($info){
			$this->createChangementEmail($info['id_u'],$info['email']);
		}

		$this->UtilisateurNewEmailSQL->delete($info['id_u']);
		$this->result = $info;
		$this->page_title = "Procédure de changement d'email";
		$this->template_milieu = "UtilisateurModifEmailConfirm";
		$this->renderDefault();
	}
	
	private function createChangementEmail($id_u,$email){
		$id_d = $this->Document->getNewId();	
		$this->Document->save($id_d,'changement-email');
		$utilisateur_info = $this->Utilisateur->getInfo($this->Authentification->getId()); 
		
		$this->Document->setTitre($id_d,$utilisateur_info['login']);
		$this->DocumentEntite->addRole($id_d,$utilisateur_info['id_e'],"editeur");
		$actionCreator = new ActionCreator($this->SQLQuery,$this->Journal,$id_d);
		$actionCreator->addAction($utilisateur_info['id_e'],$this->Authentification->getId(),Action::CREATION,"Création du document");
		
		$donneesFormulaire = $this->DonneesFormulaireFactory->get($id_d);
		foreach(array('id_u','login','nom','prenom') as $key){
			$data[$key] = $utilisateur_info[$key];
		}
		$data['email_actuel'] = $utilisateur_info['email'];
		$data['email_demande'] = $email;
		$donneesFormulaire->setTabData($data);
		
		$this->NotificationMail->notify($utilisateur_info['id_e'],$id_d,'creation','changement-email',$utilisateur_info['login']." a fait une demande de changement d'email");
		
		
	}
	
}