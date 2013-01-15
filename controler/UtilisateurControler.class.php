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
			$this->LastError->setLastError("Les utilisateurs de l'entit� racine ne peuvent pas utiliser cette proc�dure");
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
			$this->LastError->setLastError("L'email que vous avez saisi ne semble pas �tre valide");
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
		
		$this->Journal->add(Journal::MODIFICATION_UTILISATEUR,$utilisateur_info['id_e'],0,"change-email","Demande de changement d'email initi�e {$utilisateur_info['email']} -> $email");
		
		
		$this->LastMessage->setLastMessage("Un email a �t� envoy� � votre nouvelle adresse. Merci de le consulter pour la suite de la proc�dure.");
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
		$this->page_title = "Proc�dure de changement d'email";
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
		$actionCreator->addAction($utilisateur_info['id_e'],$this->Authentification->getId(),Action::CREATION,"Cr�ation du document");
		
		$donneesFormulaire = $this->DonneesFormulaireFactory->get($id_d);
		foreach(array('id_u','login','nom','prenom') as $key){
			$data[$key] = $utilisateur_info[$key];
		}
		$data['email_actuel'] = $utilisateur_info['email'];
		$data['email_demande'] = $email;
		$donneesFormulaire->setTabData($data);
		
		$this->NotificationMail->notify($utilisateur_info['id_e'],$id_d,'creation','changement-email',$utilisateur_info['login']." a fait une demande de changement d'email");
	}
	
	public function certificatAction(){
		$recuperateur = new Recuperateur($_GET);
		$this->verif_number = $recuperateur->get('verif_number');
		$this->offset = $recuperateur->getInt('offset',0);
	
		$this->limit = 20;
		
		$this->count = $this->UtilisateurListe->getNbUtilisateurByCertificat($this->verif_number);
		$this->liste = $this->utilisateurListe->getUtilisateurByCertificat($this->verif_number,$this->offset,$this->limit);
		
		if (! $this->count){
			$this->redirect("/index.php");
		}
		
		$this->certificat = new Certificat($this->liste[0]['certificat']);
		$this->certificatInfo = $this->certificat->getInfo();
		
		$this->page_title = "Certificat";
		$this->template_milieu = "UtilisateurCertificat";
		$this->renderDefault();
	}
	
	public function editionAction(){
		$recuperateur = new Recuperateur($_GET);
		$id_u = $recuperateur->get('id_u');
		$id_e = $recuperateur->getInt('id_e');
		
		$infoUtilisateur = array('login' =>  $this->LastError->getLastInput('login'),
							'nom' =>  $this->LastError->getLastInput('nom'),
							'prenom' =>  $this->LastError->getLastInput('prenom'),
							'email'=> $this->LastError->getLastInput('email'),
							'certificat' => '',
							'id_e' => $id_e,
		);
		
		if ($id_u){
			$infoUtilisateur = $this->Utilisateur->getInfo($id_u);
			if (! $infoUtilisateur){
				$this->redirect();
			}
		}
		
		$this->verifDroit($infoUtilisateur['id_e'], "utilisateur:edition");

		$this->infoEntite = $this->EntiteSQL->getInfo($infoUtilisateur['id_e']);
		$this->certificat = new Certificat($infoUtilisateur['certificat']);
		$this->arbre = $this->RoleUtilisateur->getArbreFille($this->getId_u(),"entite:edition");
		
		if ($id_u){
			$this->page_title = "Modification de " .  $infoUtilisateur['prenom']." ". $infoUtilisateur['nom'];
		} else {
			$this->page_title = "Nouvel utilisateur ";	
		}
		$this->id_u = $id_u;
		$this->id_e = $id_e;
		$this->infoUtilisateur = $infoUtilisateur;
		$this->template_milieu = "UtilisateurEdition";
		$this->renderDefault();
	}
	
	public function detailAction(){
		$recuperateur = new Recuperateur($_GET);
		$id_u = $recuperateur->get('id_u');
		
		$info = $this->Utilisateur->getInfo($id_u);
		if (! $info){
			$this->LastError->setLastError("Utilisateur $id_u inconnu");
			$this->redirect("index.php");
		}
		
		$this->certificat = new Certificat($info['certificat']);
		$this->page_title = "Utilisateur ".$info['prenom']." " . $info['nom'];
		$this->entiteListe = $this->EntiteListe;
		$this->tabEntite = $this->RoleUtilisateur->getEntite($this->getId_u(),'entite:edition');
		$this->notification = $this->Notification;
		
		$roleInfo =  $this->RoleUtilisateur->getRole($id_u);
		
		
		if ( ! $this->RoleUtilisateur->hasDroit($this->getId_u(),"utilisateur:lecture",$info['id_e'])) {
			$this->LastError->setLastError("Vous n'avez pas le droit de lecture (".$info['id_e'].")");
			$this->redirect();
		}
		
		$this->utilisateur_edition = $this->RoleUtilisateur->hasDroit($this->getId_u(),"utilisateur:edition",$info['id_e']);
		
		if( $info['id_e'] ){
			$this->infoEntiteDeBase = $this->EntiteSQL->getInfo($info['id_e']);
			$this->denominationEntiteDeBase = $this->infoEntiteDeBase['denomination'];
		}
		$this->info = $info;
		$this->id_u = $id_u;
		$this->arbre = $this->RoleUtilisateur->getArbreFille($this->getId_u(),"entite:edition");
		$this->documentTypeHTML = $this->DocumentTypeHTML;
		$this->template_milieu = "UtilisateurDetail";
		$this->renderDefault();
	}
	
	public function moiAction(){
		$id_u = $this->getId_u();
		
		$this->documentTypeHTML = $this->DocumentTypeHTML;
		
		$info = $this->Utilisateur->getInfo($id_u);
		$this->certificat = new Certificat($info['certificat']);
		
		$this->page_title = "Espace utilisateur : ".$info['prenom']." " . $info['nom'];
		
		$this->entiteListe = $this->EntiteListe;
		
		$this->tabEntite = $this->RoleUtilisateur->getEntite($this->getId_u(),'entite:edition');
		
		$this->notification = $this->Notification;
		$this->roleInfo =  $this->RoleUtilisateur->getRole($id_u);
		$this->utilisateur_edition = $this->RoleUtilisateur->hasDroit($this->getId_u(),"utilisateur:edition",$info['id_e']);
		
		if( $info['id_e'] ){
			$infoEntiteDeBase = $this->EntiteSQL->getInfo($id_u);
			$this->denominationEntiteDeBase = $infoEntiteDeBase['denomination'];
		}
		$this->info = $info;
		$this->id_u = $id_u;
		$this->arbre = $this->RoleUtilisateur->getArbreFille($this->getId_u(),"entite:lecture");
		$this->template_milieu = "UtilisateurMoi";
		$this->renderDefault();
	}
	
	private function redirectEdition($id_e,$id_u,$message){
		$lastError->setLastError("Le nom est obligatoire");
		$this->redirect("/utilisateur/edition.php?id_e=$id_e&id_u=$id_u");
	}
	
	public function editionUtilisateur($id_e,$id_u,$email,$login,$password,$password2,$nom,$prenom,$role,$certificat_content){
		$this->verifDroit($id_e, "utilisateur:edition");
		if (! $nom){
			throw new Exception("Le nom est obligatoire");
		}
		
		if (! $prenom){
			throw new Exception("Le pr�nom est obligatoire");
		}
		
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
			throw new Exception("Votre adresse email ne semble pas valide");
		}
		
		if ( $password && $password2 && ($password != $password2) ){
			throw new Exception("Les mot de passes ne correspondent pas");
		}
		
		if (! $id_u){
			$id_u = $this->UtilisateurCreator->create($login,$password,$password2,$email);
			if ( ! $id_u){
				throw new Exception($this->UtilisateurCreator->getLastError());
			}
		}
		if ( $password && $password2 ){
			$this->Utilisateur->setPassword($id_u,$password);
		}
		$oldInfo = $this->Utilisateur->getInfo($id_u);
		
		if ($certificat_content){
			$certificat = new Certificat($certificat_content);
			if ( ! $this->Utilisateur->setCertificat($id_u,$certificat)){
				$this->redirectEdition($id_e,$id_u,"Le certificat n'est pas valide");
			} 
		}
		
		$this->Utilisateur->validMailAuto($id_u);
		$this->Utilisateur->setNomPrenom($id_u,$nom,$prenom);
		$this->Utilisateur->setEmail($id_u,$email);
		$this->Utilisateur->setLogin($id_u,$login);
		$this->Utilisateur->setColBase($id_u,$id_e);
		
		$allRole = $this->RoleUtilisateur->getRole($id_u);
		if (! $allRole ){
			$this->RoleUtilisateur->addRole($id_u,RoleUtilisateur::AUCUN_DROIT,$id_e);
		}
		
		$newInfo = $this->Utilisateur->getInfo($id_u);
		
		$infoToRetrieve = array('email','login','nom','prenom');
		$infoChanged = array();
		foreach($infoToRetrieve as $key){
			if ($oldInfo[$key] != $newInfo[$key]){
				$infoChanged[] = "$key : {$oldInfo[$key]} -> {$newInfo[$key]}";
			}
		}
		$infoChanged  = implode("; ",$infoChanged);
		
		$this->Journal->add(Journal::MODIFICATION_UTILISATEUR,$id_e,$this->getId_u(),"Edit�",
		"Edition de l'utilisateur $login ($id_u) : $infoChanged");

		return $id_u;
	}
	
	public function doEditionAction(){
		$recuperateur = new Recuperateur($_POST);
		$email = $recuperateur->get('email');
		$id_e = $recuperateur->getInt('id_e');
		$id_u = $recuperateur->get('id_u');
		$login = $recuperateur->get('login');
		$password = $recuperateur->get('password');
		$password2 = $recuperateur->get('password2');
		$nom = $recuperateur->get('nom');
		$prenom = $recuperateur->get('prenom');
		$role = $recuperateur->get('role');
		$certificat_content = $this->FileUploader->getFileContent('certificat');
		
		try {
			$id_u = $this->editionUtilisateur($id_e, $id_u, $email, $login, $password, $password2, $nom, $prenom, $role, $certificat_content);
		} catch (Exception $e){
			$this->redirectEdition($id_e,$id_u,$e->getMessage());
		}
		
		$this->redirect("/utilisateur/detail.php?id_u=$id_u");
	}
	
	public function ajoutRoleAction(){
		$recuperateur = new Recuperateur($_POST);
		$id_u = $recuperateur->get('id_u');
		$role = $recuperateur->get('role');
		$id_e = $recuperateur->get('id_e',0);

		$this->verifDroit($id_e,"entite:edition");
		if ($role){
			$this->RoleUtilisateur->addRole($id_u,$role,$id_e);	
		}
		$this->redirect("/utilisateur/detail.php?id_u=$id_u");
	}
	
	public function supprimeRoleAction(){
		$recuperateur = new Recuperateur($_POST);
		$id_u = $recuperateur->get('id_u');
		$role = $recuperateur->get('role');
		$id_e = $recuperateur->getInt('id_e',0);
		$this->verifDroit($id_e,"entite:edition");
		$this->RoleUtilisateur->removeRole($id_u,$role,$id_e);
		$this->redirect("/utilisateur/detail.php?id_u=$id_u");
	}
	
}