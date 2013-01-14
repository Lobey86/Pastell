<?php
require_once( PASTELL_PATH . "/lib/helper/suivantPrecedent.php");

class DocumentControler extends PastellControler {
	
	private function redirectToList($id_e,$type = false){
		$this->redirect("/document/list.php?id_e=$id_e&type=$type");
	}
	
	private function verifDroitLecture($id_e,$id_d){
		$info = $this->Document->getInfo($id_d);
		if (!$info){
			$this->redirectToList($id_e);
		}

		if ( ! $this->RoleUtilisateur->hasDroit($this->getId_u(),$info['type'].":lecture",$id_e)) {
			$this->redirectToList($id_e,$info['type']);
		}
		
		$my_role = $this->DocumentEntite->getRole($id_e,$id_d);
		if (! $my_role ){
			$this->redirectToList($id_e,$info['type']);
		}
		return $info;
	}
	
	public function detailAction(){
		$recuperateur = new Recuperateur($_GET);
		$id_d = $recuperateur->get('id_d');
		$id_e = $recuperateur->getInt('id_e');
		$page = $recuperateur->getInt('page',0);

		$info_document = $this->verifDroitLecture($id_e, $id_d);
		$this->Journal->addConsultation($id_e,$id_d,$this->Authentification->getId());
		
		$documentType = $this->DocumentTypeFactory->getFluxDocumentType($info_document['type']);
		

		$this->info = $info_document;
		$this->id_e = $id_e;
		$this->id_d = $id_d;
		$this->page = $page;
		$this->documentType = $documentType;
		$this->infoEntite = $this->EntiteSQL->getInfo($id_e);
		$this->formulaire =  $documentType->getFormulaire();
		$this->donneesFormulaire = $this->DonneesFormulaireFactory->get($id_d,$info_document['type']);
		$this->actionPossible = $this->ActionPossible;
		$this->theAction = $documentType->getAction();
		$this->documentEntite = $this->DocumentEntite;
		$this->my_role = $this->documentEntite->getRole($id_e,$id_d);
		$this->documentEmail = $this->DocumentEmail;
		$this->documentActionEntite = $this->DocumentActionEntite;
		
		$this->page_title =  $info_document['titre'] . " (".$documentType->getName().")";;
		$this->template_milieu = "DocumentDetail"; 
		$this->renderDefault();
	}
	
	public function editionAction(){
		$recuperateur = new Recuperateur($_GET);
		$id_d = $recuperateur->get('id_d');
		$type = $recuperateur->get('type');
		$id_e = $recuperateur->getInt('id_e');
		$page = $recuperateur->getInt('page',0);
		
		$document = $this->Document;
		
		if ($id_d){
			$info = $document->getInfo($id_d);
			$type = $info['type'];
			$action = 'modification';
		} else {
			$info = array();
			$id_d = $document->getNewId();	
			$document->save($id_d,$type);
		
			$this->DocumentEntite->addRole($id_d,$id_e,"editeur");
			$actionCreator = new ActionCreator($this->SQLQuery,$this->Journal,$id_d);
			$actionCreator->addAction($id_e,$this->Authentification->getId(),Action::CREATION,"Création du document");
			$action = 'modification';
		}
		
		$this->verifDroit($id_e, $type.":edition","/document/list.php");
		
		
		$actionPossible = $this->ActionPossible;
		
		if ( ! $actionPossible->isActionPossible($id_e,$this->Authentification->getId(),$id_d,$action)) {
			$lastError->setLastError("L'action « $action »  n'est pas permise : " .$actionPossible->getLastBadRule() );
			header("Location: detail.php?id_d=$id_d&id_e=$id_e&page=$page");
			exit;
		}
		
		
		$documentType = $this->DocumentTypeFactory->getFluxDocumentType($type);
		$formulaire = $documentType->getFormulaire();
		
		
		$infoEntite = $this->EntiteSQL->getInfo($id_e);
		
		$donneesFormulaire = $this->DonneesFormulaireFactory->get($id_d,$type);
		
		$my_role = $this->documentEntite->getRole($id_e,$id_d);
		$afficheurFormulaire = new AfficheurFormulaire($formulaire,$donneesFormulaire);
		$afficheurFormulaire->setRole($my_role);
		
		$afficheurFormulaire->injectHiddenField("id_d",$id_d);
		$afficheurFormulaire->injectHiddenField("form_type",$type);
		$afficheurFormulaire->injectHiddenField("id_e",$id_e);
		
		$last_action = $this->DocumentActionEntite->getLastAction($id_e, $id_d);
		
		$editable_content = $documentType->getAction()->getEditableContent($last_action);
		
		if (!in_array($last_action,array("creation","modification")) || $editable_content){
			$afficheurFormulaire->setEditableContent($editable_content);
		}
		
		$this->page_title="Edition d'un document « " . $documentType->getName() . " » ( " . $infoEntite['denomination'] . " ) ";
		
		$this->info = $info;
		$this->id_e = $id_e;
		$this->id_d = $id_d;
		$this->page = $page;
		$this->type = $type;
		$this->documentType = $documentType;
		$this->infoEntite = $this->EntiteSQL->getInfo($id_e);
		$this->formulaire =  $documentType->getFormulaire();
		$this->donneesFormulaire = $donneesFormulaire;
		$this->actionPossible = $this->ActionPossible;
		$this->theAction = $documentType->getAction();
		$this->documentEntite = $this->DocumentEntite;
		$this->my_role = $this->documentEntite->getRole($id_e,$id_d);
		$this->documentEmail = $this->DocumentEmail;
		$this->documentActionEntite = $this->DocumentActionEntite;
		$this->afficheurFormulaire = $afficheurFormulaire;
		$this->template_milieu = "DocumentEdition"; 
		$this->renderDefault();
	}
	
	
	public function indexAction(){
		$recuperateur = new Recuperateur($_GET);
		$id_e = $recuperateur->get('id_e',0);
		$offset = $recuperateur->getInt('offset',0);
		$search = $recuperateur->get('search');
		$limit = 20;
		
		$liste_type = array();
		$allDroit = $this->RoleUtilisateur->getAllDroit($this->getId_u());
		foreach($allDroit as $droit){
			if (preg_match('/^(.*):lecture$/',$droit,$result)){
				$liste_type[] = $result[1];
			}
		}	
		
		$liste_collectivite = $this->roleUtilisateur->getEntite($this->getId_u(),"entite:lecture");
		
		if (! $id_e ) {
			if (count($liste_collectivite) == 0){
				$this->redirect("/nodroit.php");
			}
			if (count($liste_collectivite) == 1){
				$id_e = $liste_collectivite[0];
			}
		}
		
		if ($id_e){						
			$this->listDocument = $this->DocumentActionEntite->getListDocumentByEntite($id_e,$liste_type,$offset,$limit,$search);
			$this->count = $this->DocumentActionEntite->getNbDocumentByEntite($id_e,$liste_type,$search);
		}
		
		$this->infoEntite = $this->EntiteSQL->getInfo($id_e);
		$this->id_e = $id_e;
		$this->search = $search;
		$this->offset = $offset;
		$this->limit = $limit;
		$this->documentListAfficheur = $this->DocumentListAfficheur;
		
		$this->setNavigationInfo($id_e,"document/index.php?a=a");
		$this->page_title= "Liste des documents <em>" . $this->infoEntite['denomination'] ."</em>";
		$this->template_milieu = "DocumentIndex"; 
		$this->renderDefault();
	}

	public function listAction(){
		
		$recuperateur = new Recuperateur($_GET);
		$id_e = $recuperateur->get('id_e',0);
		$offset = $recuperateur->getInt('offset',0);
		$search = $recuperateur->get('search');
		$type = $recuperateur->get('type');
		$filtre = $recuperateur->get('filtre');
		$last_id = $recuperateur->get('last_id');
		
		$limit = 20;
		
		if (! $type){
			$this->redirect("/document/index.php?id_e=$id_e");
		}
		
		
		$documentType = $this->DocumentTypeFactory->getFluxDocumentType($type);
		
		$liste_collectivite = $this->RoleUtilisateur->getEntite($this->getId_u(),$type.":lecture");
		
		if ( ! $liste_collectivite){
			$this->redirect("/document/index.php");
		}
		
		if (!$id_e && (count($liste_collectivite) == 1)){
				$id_e = $liste_collectivite[0];
		}
			
		$this->verifDroit($id_e, "$type:lecture");
		$this->infoEntite = $this->EntiteSQL->getInfo($id_e);
		
		$this->page_title = "Liste des documents " . $documentType->getName();
		if ($id_e){
			$this->page_title .= " pour " . $this->infoEntite['denomination'];
		}
		
		$this->documentActionEntite = $this->DocumentActionEntite;
		$this->actionPossible = $this->ActionPossible;
		
		
		$this->all_action = $documentType->getAction()->getWorkflowAction();
		
		
		if ($this->actionPossible->isCreationPossible($id_e,$this->getId_u(),$type)){
			$this->nouveau_bouton_url = "document/edition.php?type=$type&id_e=$id_e";
		}
		$this->id_e = $id_e;
		$this->search = $search;
		$this->offset = $offset;
		$this->limit = $limit;
		$this->filtre = $filtre;
		$this->last_id = $last_id;
		$this->type = $type;
		$this->documentTypeFactory = $this->DocumentTypeFactory;
		$this->setNavigationInfo($id_e,"document/list.php?type=$type");
				
		$this->template_milieu = "DocumentList"; 
		$this->renderDefault();
	}
	
	public function searchAction(){
				
		$recuperateur = new Recuperateur($_GET);
		$this->id_e = $recuperateur->get('id_e',0);
		$this->type = $recuperateur->get('type');
		$this->lastEtat = $recuperateur->get('lastetat');
		$this->last_state_begin = $recuperateur->get('last_state_begin');
		$this->last_state_end = $recuperateur->get('last_state_end');
		$this->last_state_begin_iso = getDateIso($this->last_state_begin );
		$this->last_state_end_iso = getDateIso($this->last_state_end);
		
		$this->etatTransit = $recuperateur->get('etatTransit');
		$this->state_begin =  $recuperateur->get('state_begin');
		$this->state_end =  $recuperateur->get('state_end');
		$this->tri =  $recuperateur->get('tri');
		$this->go = $recuperateur->get('go',0);
		$this->offset = $recuperateur->getInt('offset',0);
		$this->search = $recuperateur->get('search');
		
		$this->limit = 20;
		
		$allDroit = $this->RoleUtilisateur->getAllDroit($this->getId_u());		
		$this->arbre = $this->RoleUtilisateur->getArbreFille($this->getId_u(),"entite:lecture");
		
		$this->listeEtat = $this->DocumentTypeFactory->getActionByRole($allDroit);
		
		$this->DocumentTypeHTML->setDroit($allDroit);
		$this->liste_type = $this->FluxDefinitionFiles->getTypeByDroit($allDroit);
		$this->documentTypeHTML = $this->DocumentTypeHTML;
		$this->documentActionEntite = $this->DocumentActionEntite;
		$this->documentTypeFactory = $this->DocumentTypeFactory;
		
		$this->page_title= "Recherche avancée de document";
		$this->template_milieu = "DocumentSearch"; 
		$this->renderDefault();
	}
	
	public function warningAction(){
		$recuperateur = new Recuperateur($_GET);
		$this->id_d = $recuperateur->get('id_d');
		$this->action = $recuperateur->get('action');
		$this->id_e = $recuperateur->get('id_e');
		$this->page = $recuperateur->getInt('page',0);
		
		
		$this->infoDocument = $this->Document->getInfo($this->id_d);
		
		$type = $this->infoDocument['type'];
		$documentType = $this->DocumentTypeFactory->getFluxDocumentType($type);
		$theAction = $documentType->getAction();
		
		$this->actionName = $theAction->getDoActionName($this->action);
		
		$this->page_title= "Attention ! Action irréversible";
		$this->template_milieu = "DocumentWarning"; 
		$this->renderDefault();
	}
}