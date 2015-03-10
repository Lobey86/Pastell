<?php 
class FournisseurControler extends PastellControler {
	
	/**
	 * @return MailFournisseurInvitation
	 */
	public function getMailFournisseurInvitation($id_e){
		return $this->ConnecteurFactory->getConnecteurByType($id_e,"fournisseur-invitation" ,"mail-fournisseur-invitation");
	}
	
	private function testFournisseurInvitation($id_e,$id_d,$secret){
		$mailFournisseurInvitation = $this->getMailFournisseurInvitation($id_e);
		$erreur = "";
		
		if (! $mailFournisseurInvitation) {
			$this->LastError->setLastError("Un problème sur la collectivité empêche de terminer votre inscription");
			return false;
		} 
		if(! $mailFournisseurInvitation->verifSecret($this->DonneesFormulaireFactory->get($id_d),$secret)){
			$this->LastError->setLastError("Un problème de validation empêche de terminer votre inscription");
			return false;
		}
		$documentActionInfo = $this->DocumentActionSQL->getLastActionInfo($id_d,$id_e);
		if ($documentActionInfo['action'] == 'fournisseur-inscrit'){
			$this->LastError->setLastError("Vous êtes déjà inscrit sur la plateforme à partir de cet email");
			return false;	
		}
		return true;
	}
	
	public function preInscriptionAction(){
		$recuperateur = new Recuperateur($_GET);
		$this->id_e = $recuperateur->getInt('id_e');
		$this->id_d = $recuperateur->get('id_d');
		$this->secret = $recuperateur->get('s');
		
		
		if ($this->testFournisseurInvitation($this->id_e, $this->id_d, $this->secret)){
			$this->has_error = false;
			$donneesFormulaire = $this->DonneesFormulaireFactory->get($this->id_d);
			$this->email = $donneesFormulaire->get('email');
			$this->raison_sociale = $donneesFormulaire->get('raison_sociale');
		} else {
			$this->has_error = true;
		}
		
		$this->page_title = "Bienvenue sur Pastell";
		$this->template_milieu = "FournisseurPreInscription";
		$this->renderDefault();
	}
	
	private function redirectWithError($url_redirect,$error_message){
		$this->LastError->setLastError($error_message);
		$this->redirect($url_redirect);
	} 
	
	public function doInscriptionAction(){
		$recuperateur = new Recuperateur($_POST);
		$id_e = $recuperateur->getInt('id_e');
		$id_d = $recuperateur->get('id_d');
		$secret = $recuperateur->get('s');
		$url_redirect = "fournisseur/pre-inscription.php?id_e=$id_e&id_d=$id_d&s=$secret";
		if (! $this->testFournisseurInvitation($id_e, $id_d, $secret)){
			$this->redirect($url_redirect);
		}
		$email = $recuperateur->get('email');
		$siren = $recuperateur->get('siren');
		$login = $recuperateur->get('login');
		$password = $recuperateur->get('password');
		$password2 = $recuperateur->get('password2');
		$nom = $recuperateur->get('nom');
		$prenom = $recuperateur->get('prenom');
		$denomination = $recuperateur->get('denomination');
		
		if (!$login) {
			$this->redirectWithError($url_redirect,"L'identifiant est obligatoire");
		}
		if ($this->EntiteSQL->getBySiren($siren)){
			$this->redirectWithError($url_redirect,"Le siren que vous avez déjà indiqué est déjà connu sur la plateforme");
		}

		$sirenVerifier = new Siren();
		if (! $this->Siren->isValid($siren)){
			$this->redirectWithError($url_redirect,"Votre siren ne semble pas valide");
		}
		if ( ! $denomination ){
			$this->redirectWithError($url_redirect,"Il faut saisir une raison sociale");
		}

		$id_u = $this->UtilisateurCreator->create($login,$password,$password2,$email);
		if ( ! $id_u){
			$this->redirectWithError($url_redirect,$this->UtilisateurCreator->getLastError());
		}
		$this->Utilisateur->setNomPrenom($id_u,$nom,$prenom);
		$this->Utilisateur->validMailAuto($id_u);
		$new_id_e = $this->EntiteCreator->edit(false,$siren,$denomination,Entite::TYPE_FOURNISSEUR,0,0);
		$this->RoleUtilisateur->addRole($id_u,"fournisseur",$new_id_e);
		
		$this->ActionChange->addAction($id_d,$new_id_e,$id_u,'fournisseur-inscrit',"Le fournisseur s'est inscrit avec le siren $siren et la raison sociale $denomination");
		$this->CollectiviteFournisseurSQL->add($id_e,$new_id_e);
		
		$info = array();
		$new_id_d = $this->Document->getNewId();
		$this->Document->save($new_id_d,'fournisseur-inscription');
		$this->Document->setTitre($new_id_d,$denomination);
		$this->DocumentEntite->addRole($new_id_d,$new_id_e,"editeur");
		$this->ActionChange->addAction($new_id_d,$new_id_e,$id_u,Action::CREATION,"Création du document");
		
		$fournisseurInscription = $this->DonneesFormulaireFactory->get($new_id_d);
		$fournisseurInscription->setData('siren',$siren);
		$fournisseurInscription->setData('raison_sociale',$denomination);
		
		
		$this->LastMessage->setLastMessage("Votre inscription est terminée, vous pouvez vous connecter");
		$this->redirect("connexion/connexion.php");
	}
	
	public function dejaInscritAction(){
		$recuperateur = new Recuperateur($_POST);
		$id_e = $recuperateur->getInt('id_e');
		$id_d = $recuperateur->get('id_d');
		$secret = $recuperateur->get('s');
		$url_redirect = "fournisseur/pre-inscription.php?id_e=$id_e&id_d=$id_d&s=$secret";
		if (! $this->testFournisseurInvitation($id_e, $id_d, $secret)){
			$this->redirect($url_redirect);
		}
		$login = $recuperateur->get('login');
		$password = $recuperateur->get('password');
		$id_u = $this->ConnexionControler->connexionActionRedirect("fournisseur/pre-inscription.php?id_e=$id_e&id_d=$id_d&s=$secret");
		
		$liste_entite = $this->RoleUtilisateur->getEntite($id_u,'fournisseur-inscription:edition');
		if (count($liste_entite) != 1){
			$this->redirectWithError($url_redirect,"Un problème a empêché l'inscription de cette collectivité.");
		}
		$id_e_fournisseur = $liste_entite[0];
		$this->CollectiviteFournisseurSQL->add($id_e,$id_e_fournisseur);
		//$this->ActionChange->addAction($id_d,$id_e_fournisseur,$id_u,'fournisseur-inscrit',"Le fournisseur s'est inscrit avec le siren $siren et la raison sociale $denomination");
		
		$entiteInfo = $this->EntiteSQL->getInfo($id_e);
		$this->LastMessage->setLastMessage("La collectivité {$entiteInfo['denomination']} a été ajouté à la liste. Veuillez soumettre vos informations (formulaire d'adhésion) à la collectivité.");
		$this->redirect("document/index.php");
	}
	
	
}