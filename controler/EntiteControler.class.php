<?php


class EntiteControler extends PastellControler {
	
	
	public function listUtilisateur(){
		$recuperateur = new Recuperateur($_GET);
		$id_e = $recuperateur->getInt('id_e',0);
		$descendance = $recuperateur->get('descendance');
		$role = $recuperateur->get('role');
		$search = $recuperateur->get('search');
		$offset = $recuperateur->getInt('offset');
		$this->hasDroitLecture($id_e);
		
		$all_role = $this->RoleSQL->getAllRole();
		$all_role[] = array('role' => RoleUtilisateur::AUCUN_DROIT,'libelle'=> RoleUtilisateur::AUCUN_DROIT);

		$this->all_role = $all_role;
		$this->droitEdition = $this->RoleUtilisateur->hasDroit($this->Authentification->getId(),"utilisateur:edition",$id_e);
		$this->nb_utilisateur = $this->UtilisateurListe->getNbUtilisateur($id_e,$descendance,$role,$search);
		$this->liste_utilisateur = $this->UtilisateurListe->getAllUtilisateur($id_e,$descendance,$role,$search,$offset);
		$this->id_e = $id_e;
		$this->role_selected = $role;
		$this->offset = $offset;
		$this->search=$search;
		$this->descendance = $descendance;
		
		
		$this->render("UtilisateurList");
	}
	
	public function exportUtilisateur(){
		$recuperateur = new Recuperateur($_GET);
		$id_e = $recuperateur->getInt('id_e',0);
		$descendance = $recuperateur->get('descendance');
		$the_role = $recuperateur->get('role');
		$search = $recuperateur->get('search');
		
		$this->hasDroitLecture($id_e);
		
		$result = array();
		$result[] = array("id_u","login","prénom","nom","email","collectivité de base","id_e","rôles");
		
		$allUtilisateur = $this->UtilisateurListe->getAllUtilisateur($id_e,$descendance,$the_role,$search,-1);		
		foreach($allUtilisateur as $i => $user){
			$r = array();
			foreach($user['all_role'] as $role){
				$r[] = ($role['libelle']?:"Aucun droit") . " - ".($role['denomination']?:'Entite racine');  
			}
			$user['all_role'] = implode(",",$r);
			$result[]  = array($user['id_u'],$user['login'],
				$user['prenom'],$user['nom'],$user['email'],
				$user['denomination']?:"Entité racine",$user['id_e'],$user['all_role']);
		}
		
		$filename = "utilisateur-pastell-$id_e-$descendance-$the_role-$search.csv";
		
		$this->CSVoutput->send($filename,$result);
	}
	
	public function detailEntite(){		
		$recuperateur = new Recuperateur($_GET);
		$id_e = $recuperateur->getInt('id_e',0);
		$this->hasDroitLecture($id_e);
		$info = $this->EntiteSQL->getInfo($id_e);
		$this->droit_edition = $this->RoleUtilisateur->hasDroit($this->Authentification->getId(),"entite:edition",$id_e);
		$this->droit_lecture_cdg = (isset($info['cdg']['id_e']) && $this->RoleUtilisateur->hasDroit($authentification->getId(),"entite:lecture",$info['cdg']['id_e']));
		$this->entiteExtendedInfo = $this->EntiteSQL->getExtendedInfo($id_e);
		$this->has_ged  = $this->EntitePropertiesSQL->getProperties($id_e,EntitePropertiesSQL::ALL_FLUX,'has_ged');
		$this->has_sae = $this->EntitePropertiesSQL->getProperties($id_e,EntitePropertiesSQL::ALL_FLUX,'has_archivage');
		
		$this->render("EntiteDetail");
		
	}
	
	public function listEntite(){
		$recuperateur = new Recuperateur($_GET);
		$offset = $recuperateur->getInt('offset',0);
		$search = $recuperateur->get('search','');
		
		$liste_collectivite = $this->RoleUtilisateur->getEntiteWithDenomination($this->Authentification->getId(),'entite:lecture');
		$nbCollectivite = count($liste_collectivite);
	
		if ($nbCollectivite == 1){
			if ($liste_collectivite[0]['id_e'] == 0 ) {
				$liste_collectivite = $this->EntiteListe->getAllCollectivite($offset,$search);
				$nbCollectivite = $this->EntiteListe->getNbCollectivite($search);
			} else {
				$this->redirect("/entite/detail.php?id_e=".$liste_collectivite[0]['id_e']);
			}
		}
		$this->liste_collectivite = $liste_collectivite;
		$this->nbCollectivite = $nbCollectivite;
		$this->search = $search;
		$this->offset = $offset;
		$this->render("EntiteList");	
	}
	
	
}