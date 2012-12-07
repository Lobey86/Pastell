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
		
		$allUtilisateur = $this->UtilisateurListe->getAllUtilisateur($id_e,$descendance,$role,$search,$offset);
		
		$all_role = $this->RoleSQL->getAllRole();
		$all_role[] = array('role' => RoleUtilisateur::AUCUN_DROIT,'libelle'=> RoleUtilisateur::AUCUN_DROIT);
		
		$utilisateurListeHTML = $this->UtilisateurListeHTML;
		$utilisateurListeHTML->addRole($all_role);
		
		if ($this->RoleUtilisateur->hasDroit($this->Authentification->getId(),"utilisateur:edition",$id_e)){
			$utilisateurListeHTML->addDroitEdition();
		}
		
		$nb_utilisateur = $this->UtilisateurListe->getNbUtilisateur($id_e,$descendance,$role,$search);
		$utilisateurListeHTML->display($allUtilisateur,$id_e,$role,$descendance,$id_e?"entite/detail.php":"entite/detail0.php",1,$search,$offset,$nb_utilisateur);
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
	
}