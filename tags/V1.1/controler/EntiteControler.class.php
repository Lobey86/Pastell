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
		$this->tableau_milieu = "UtilisateurList";
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
		$this->tableau_milieu = "EntiteDetail";
	}
	
	public function hasManyCollectivite(){
		$liste_collectivite = $this->RoleUtilisateur->getEntiteWithDenomination($this->Authentification->getId(),'entite:lecture');
		$nbCollectivite = count($liste_collectivite);
		if ($nbCollectivite == 1){
			return ($liste_collectivite[0]['id_e'] == 0 );
		}
		return true;
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
		$this->tableau_milieu = "EntiteList";
	}
	
	public function importAction(){
		$recuperateur = new Recuperateur($_GET);
		$id_e = $recuperateur->getInt('id_e',0);
		$page =  $recuperateur->getInt('page',0);
		$this->hasDroitEdition($id_e);
		$this->entite_info = $this->EntiteSQL->getInfo($id_e);
		$this->template_milieu = "EntiteImport";
		$this->page_title = "Importer"; 
		
		if ($page == 0){
			$this->allCDG = $this->EntiteListe->getAll(Entite::TYPE_CENTRE_DE_GESTION);
			$this->cdg_selected = false;
		}
		
		$this->onglet_tab = array("Collectivités","Agents","Grades");
		$onglet_content = array("EntiteImportCollectivite","EntiteImportAgent","EntiteImportGrade");
		$this->template_onglet = $onglet_content[$page];
		$this->page = $page;
		
		$this->renderDefault();
	}
	
	public function editionAction(){		
		$recuperateur = new Recuperateur($_GET);
		$entite_mere = $recuperateur->getInt('entite_mere',0);
		$id_e = $recuperateur->getInt('id_e',0);
		$this->hasDroitEdition($id_e);
		$this->hasDroitEdition($entite_mere);
	
		if ($id_e){
			$infoEntite = $this->EntiteSQL->getInfo($id_e);
			$infoEntite['centre_de_gestion'] = $this->EntiteSQL->getCDG($id_e);
			$infoEntite['has_ged'] = $this->EntitePropertiesSQL->getProperties($id_e,EntitePropertiesSQL::ALL_FLUX,'has_ged');
			$infoEntite['has_archivage'] = $this->EntitePropertiesSQL->getProperties($id_e,EntitePropertiesSQL::ALL_FLUX,'has_archivage');			
			$this->page_title = "Modification de " . $infoEntite['denomination'];
		} else {
			$infoEntite = $this->getEntiteInfoFromLastError();
			if ($entite_mere){
				$this->infoMere = $this->EntiteSQL->getInfo($entite_mere);
				$this->page_title = "Nouvelle fille pour " . $this->infoMere['denomination'];
			} else {
				$this->page_title = "Création d'une collectivité";
			} 
		}
		$this->infoEntite = $infoEntite;
		$this->cdg_selected = $infoEntite['centre_de_gestion'];
		$this->allCDG = $this->EntiteListe->getAll(Entite::TYPE_CENTRE_DE_GESTION);
		$this->template_milieu = "EntiteEdition";
		$this->id_e = $id_e;
		$this->entite_mere = $entite_mere;
		
		$this->renderDefault();
	}
	
	private function getEntiteInfoFromLastError(){
		$field_list = array("type","denomination","siren","entite_mere","id_e","has_ged","has_archivage","centre_de_gestion");
		foreach($field_list as $field){
			$infoEntite[$field] = $this->LastError->getLastInput($field);
		}
		return $infoEntite;
	}
	
	public function detailAction(){
		$recuperateur = new Recuperateur($_GET);
		$this->id_e = $recuperateur->getInt('id_e',0);
		$this->tab_number = $recuperateur->getInt('page',0);
		
		$this->has_many_collectivite = $this->hasManyCollectivite();
		$this->info = $this->EntiteSQL->getInfo($this->id_e);
		
		if ($this->id_e){
			$this->page_title = "Détail " . $this->info['denomination'];
			$this->formulaire_tab = array("Informations générales","Utilisateurs","Agents","Connecteurs","Flux","Annuaire" );
		} else {
			$this->formulaire_tab = array("Entité","Utilisateurs","Agents","Connecteurs globaux","Associations connecteurs" ,"Annuaire" );
			$this->page_title = "Administration";
		}
		
		switch($this->tab_number){
			case 0:
				if ($this->id_e){ 
					$this->detailEntite();
				} else {	
					$this->listEntite();
				}
				break;
			case 1: 
				$this->listUtilisateur();
				break;
			case 2:
				$this->listAgent();
				break;
			case 3:
				$this->listConnecteur();
				break;
			case 4:
				$this->listFlux();
				break;
		}
		
		$this->template_milieu = "EntiteIndex";
		$this->renderDefault();
	}
	
	public function choixAction(){
		$recuperateur = new Recuperateur($_GET);
		$this->id_d = $recuperateur->get('id_d');
		$this->id_e =  $recuperateur->get('id_e');
		$this->action = $recuperateur->get('action');
		$this->type = $recuperateur->get('type',Entite::TYPE_COLLECTIVITE);
		
		if ($this->type == 'service'){
			$this->liste = $this->EntiteListe->getAllDescendant($this->id_e);
		} else {
			$this->liste  = $this->EntiteListe->getAll($this->type);
		}
		
		if (! $this->liste ) {
			$this->LastError->setLastError("Aucune entité ({$this->type}) n'est disponible pour cette action");
			$this->redirect("/document/detail.php?id_e={$this->id_e}&id_d={$this->id_d}");
		}
		$this->page_title = "Veuillez choisir le ou les destinataires du document ";
		$this->template_milieu = "EntiteChoix";
		$this->renderDefault();
	}
	
	public function edition($id_e,$nom,$siren,$type,$entite_mere,$centre_de_gestion,$has_ged,$has_archivage){
		if ($id_e){
			$this->hasDroitEdition($id_e);	
		}
		$this->hasDroitEdition($entite_mere);
		
		if (!$nom){
			throw new Exception("Le nom est obligatoire");
		}
		
		if ($type == Entite::TYPE_SERVICE && ! $entite_mere){
			throw new Exception("Un service doit être ataché à une entité mère (collectivité, centre de gestion ou service)");
		}
		
		if ($type != Entite::TYPE_SERVICE) {
			if ( ! $siren ){
				throw new Exception("Le siren est obligatoire");
			} 
					
			if (  ! ( $this->Siren->isValid($siren) || ($id_e && $this->EntiteSQL->exists($id_e)))){
				throw new Exception("Votre siren ne semble pas valide");
			}
		} 
		
		
		if ( ! $id_e && $siren){
			if ($this->EntiteListe->getBySiren($siren)){
				throw new Exception("Ce SIREN est déjà utilisé");
			}
		}
		
		$id_e = $this->EntiteCreator->edit($id_e,$siren,$nom,$type,$entite_mere,$centre_de_gestion);
		$this->EntitePropertiesSQL->setProperties($id_e,EntitePropertiesSQL::ALL_FLUX,'has_ged',$has_ged);
		$this->EntitePropertiesSQL->setProperties($id_e,EntitePropertiesSQL::ALL_FLUX,'has_archivage',$has_archivage);
		return $id_e;
	}
	
	public function doEditionAction(){
		$recuperateur = new Recuperateur($_POST);
		$id_e = $recuperateur->get('id_e');
		$nom = $recuperateur->get('denomination');
		$siren = $recuperateur->get('siren',0);
		$type = $recuperateur->get('type');
		$entite_mere =  $recuperateur->get('entite_mere',0);
		$centre_de_gestion =  $recuperateur->get('centre_de_gestion',0);
		$has_ged = $recuperateur->get('has_ged',0);
		$has_archivage = $recuperateur->get('has_archivage',0);
		try {
			$id_e = $this->edition($id_e, $nom, $siren, $type, $entite_mere, $centre_de_gestion, $has_ged, $has_archivage);
		} catch(Exception $e){
			$this->LastError->setLastError($e->getMessage());
			$this->redirect("/entite/edition.php?id_e=$id_e");
		}
		
		$this->LastError->deleteLastInput();
		$this->redirect("/entite/detail.php?id_e=$id_e");
	}
	public function listAgent(){
		$recuperateur = new Recuperateur($_GET);
		$id_e = $recuperateur->getInt('id_e',0);
		$offset = $recuperateur->getInt('offset',0);
		$page = $recuperateur->getInt('page',0);
		$search = $recuperateur->get('search');
		
		$this->hasDroitLecture($id_e);
		$info = $this->EntiteSQL->getInfo($id_e);
		$id_ancetre = $this->EntiteSQL->getCollectiviteAncetre($id_e);
		if ($id_ancetre == $id_e){
			$siren = $info['siren'];
		} else {
			$this->infoAncetre = $this->EntiteSQL->getInfo($id_ancetre);
			$siren = $this->infoAncetre['siren'];
		}
		if ($id_e){
			$this->nbAgent = $this->AgentSQL->getNbAgent($siren,$search);
			$this->listAgent = $this->AgentSQL->getBySiren($siren,$offset,$search);
		} else {
			$this->nbAgent = $this->AgentSQL->getNbAllAgent($search);
			$this->listAgent = $this->AgentSQL->getAllAgent($search,$offset);
		}
		$this->offset = $offset;
		$this->page = $page;
		$this->id_ancetre = $id_ancetre;
		$this->droit_edition = $this->RoleUtilisateur->hasDroit($this->Authentification->getId(),"entite:edition",$id_e);
		$this->id_e = $id_e;
		$this->search = $search;
		$this->tableau_milieu = "AgentList";
	}
	
	public function listConnecteur(){
		$recuperateur = new Recuperateur($_GET);
		$id_e = $recuperateur->getInt('id_e',0);
		$this->hasDroitLecture($id_e);
		$this->id_e = $id_e;
		$this->all_connecteur = $this->ConnecteurEntiteSQL->getAll($id_e);
		$this->tableau_milieu = "ConnecteurList";
	}
	
	public function listFlux(){
		$recuperateur = new Recuperateur($_POST);
		$id_e = $recuperateur->getInt('id_e');
		$this->hasDroitLecture($id_e);
		$this->id_e = $id_e;
		$this->all_flux_entite = $this->FluxEntiteSQL->getAll($id_e);
		
		
		if ($id_e){
			$this->all_flux = $this->FluxDefinitionFiles->getAll();
			$this->tableau_milieu = "FluxList";
		} else {
			$all_connecteur_type = $this->ConnecteurDefinitionFiles->getAllGlobalType();
			$all_type = array();
			foreach($all_connecteur_type as $connecteur_type){
				try {
					$global_connecteur = $this->ConnecteurFactory->getGlobalConnecteur($connecteur_type);
				} catch (Exception $e){
					$global_connecteur =  false;
				}
				$all_type[$connecteur_type] = $global_connecteur;
			}
				
			$this->all_connecteur_type = $all_type;
			if (isset($this->all_flux_entite['global'])){
				$this->all_flux_global = $this->all_flux_entite['global'];
			} else {
				$this->all_flux_global = array();	
			}
			$this->tableau_milieu = "FluxGlobalList";
		}
	}
	
	
}