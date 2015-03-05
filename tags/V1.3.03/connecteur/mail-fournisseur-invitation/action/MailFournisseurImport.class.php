<?php
class MailFournisseurImport extends ActionExecutor {
	
	
	public function go(){
		
		$properties = $this->getConnecteurProperties();
		
		$fichier_fournisseur_path = $properties->getFilePath("fichier_fournisseur");
		
		if (! $fichier_fournisseur_path){
			$this->setLastMessage("Aucun fichier de fournisseur n'a été fourni");
			return false;
		}
		
		$info = $this->objectInstancier->CSV->get($fichier_fournisseur_path);
		if (!$info){
			$this->setLastMessage("Le fichier fourni est vide ou n'est pas en CSV");
			return false;
		}
		
		
		foreach($info as $fournisseur_info){

			if (count($fournisseur_info)<2){
				continue;
			}
				
			$raison_sociale = $fournisseur_info[0];
			$email = $fournisseur_info[1];
				
			if ($this->getDocument()->getIdFromEntiteAndTitre($this->id_e,$raison_sociale, 'fournisseur-invitation')){
				continue;
			}
			$id_d = $this->getDocument()->getNewId();
			$action = 'creation';
			$this->getDocument()->save($id_d, 'fournisseur-invitation');
			$this->getDocument()->setTitre($id_d, $raison_sociale);
			$donneesFormulaire = $this->getDonneesFormulaireFactory()->get($id_d,'fournisseur-invitation');
			$donneesFormulaire->setData('raison_sociale', $raison_sociale);
			$donneesFormulaire->setData('email', $email);
			$this->getDocumentEntite()->addRole($id_d,$this->id_e,"editeur");
			$this->getActionCreator($id_d)->addAction($this->id_e, $this->id_u, Action::CREATION,"Création du document (import fichier)");
			
				
		}
		
		$this->setLastMessage("Les flux « invitation fournisseur » ont été créés");
		return true;	
	}
	
}