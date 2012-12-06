<?php
class EntiteControler extends PastellControler {
	
	public function listUtilisateur(){
		
		$recuperateur = new Recuperateur($_GET);
		$id_e = $recuperateur->getInt('id_e',0);
		$descendance = $recuperateur->get('descendance');
		$role = $recuperateur->get('role');
		$search = $recuperateur->get('search');
		$offset = $recuperateur->getInt('offset');
		
			
		$all_role = $this->RoleSQL->getAllRole();
		$all_role[] = array('role' => RoleUtilisateur::AUCUN_DROIT,'libelle'=> RoleUtilisateur::AUCUN_DROIT);
		
		$utilisateurListe = $this->UtilisateurListe;
		
		$utilisateurListeHTML = $this->UtilisateurListeHTML;
		$utilisateurListeHTML->addRole($all_role);
		
		if ($this->RoleUtilisateur->hasDroit($this->Authentification->getId(),"utilisateur:edition",$id_e)){
			$utilisateurListeHTML->addDroitEdition();
		}

		$nb_utilisateur = $utilisateurListe->getNbUtilisateur($id_e,$descendance,$role,$search);
		
		$allUtilisateur = $utilisateurListe->getAllUtilisateur($id_e,$descendance,$role,$search,$offset);
		
		$utilisateurListeHTML->display($allUtilisateur,$id_e,$role,$descendance,$id_e?"entite/detail.php":"entite/detail0.php",$id_e?1:0,$search,$offset,$nb_utilisateur);
		
	}
	
}