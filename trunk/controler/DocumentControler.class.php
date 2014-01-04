<?php

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
	
	public function arAction(){
		$recuperateur = new Recuperateur($_GET);
		$id_d = $recuperateur->get('id_d');
		$id_e = $recuperateur->getInt('id_e');
		
		$info_document = $this->verifDroitLecture($id_e, $id_d);
		

		$true_last_action = $this->DocumentActionEntite->getTrueAction($id_e, $id_d);
		$documentType = $this->DocumentTypeFactory->getFluxDocumentType($info_document['type']);
		
 		$action = $documentType->getAction();
		if (! $action->getProperties($true_last_action,'accuse_de_reception_action')){
			$this->redirect("/document/detail.php?id_e=$id_e&id_d=$id_d");
		}
		$this->action = $action->getProperties($true_last_action,'accuse_de_reception_action');
		$this->id_e = $id_e;
		$this->id_d = $id_d;
		
		$this->page_title = "Accusé de réception";
		$this->template_milieu = "DocumentAR";
		$this->renderDefault();
		
	}
	
	public function detailAction(){
		$recuperateur = new Recuperateur($_GET);
		$id_d = $recuperateur->get('id_d');
		$id_e = $recuperateur->getInt('id_e');
		$page = $recuperateur->getInt('page',0);

		$info_document = $this->verifDroitLecture($id_e, $id_d);
		
		$documentType = $this->DocumentTypeFactory->getFluxDocumentType($info_document['type']);
		
		$true_last_action = $this->DocumentActionEntite->getTrueAction($id_e, $id_d);
		
 		$action = $documentType->getAction();
		if ($action->getProperties($true_last_action,'accuse_de_reception_action')){
			/*$this->LastMessage->setLastMessage("Vous devez accusé reception du message");
			$this->redirect("/document/list.php?id_e=$id_e&type={$info_document['type']}");*/
			$this->redirect("/document/ar.php?id_e=$id_e&id_d=$id_d");
		}
		
		$this->Journal->addConsultation($id_e,$id_d,$this->Authentification->getId());
		
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
		$action = $recuperateur->get('action');
		
		$document = $this->Document;
		
		if ($action){
			$info = $document->getInfo($id_d);
			$type = $info['type'];
		}elseif ($id_d){
			$info = $document->getInfo($id_d);
			$type = $info['type'];
			$action = 'modification';
		} else {
			$info = array();
			$id_d = $document->getNewId();	
			$document->save($id_d,$type);
		
			$this->DocumentEntite->addRole($id_d,$id_e,"editeur");
			$this->ActionChange->addAction($id_d,$id_e,$this->Authentification->getId(),Action::CREATION,"Création du document");
			$action = 'modification';
		}
		
		$this->verifDroit($id_e, $type.":edition","/document/list.php");
		
		
		$actionPossible = $this->ActionPossible;
		
		if ( ! $actionPossible->isActionPossible($id_e,$this->Authentification->getId(),$id_d,$action)) {
			$this->LastError->setLastError("L'action « $action »  n'est pas permise : " .$actionPossible->getLastBadRule() );
			header("Location: detail.php?id_d=$id_d&id_e=$id_e&page=$page");
			exit;
		}
		
		
		$documentType = $this->DocumentTypeFactory->getFluxDocumentType($type);
		$formulaire = $documentType->getFormulaire();
		

		$infoEntite = $this->EntiteSQL->getInfo($id_e);
		
		$donneesFormulaire = $this->DonneesFormulaireFactory->get($id_d,$type);
		
		$formulaire->addDonnesFormulaire($donneesFormulaire);
		
		if (! $formulaire->tabNumberExists($page)){
			$page = 0;
		}
		
		
		$my_role = $this->documentEntite->getRole($id_e,$id_d);
		$afficheurFormulaire = new AfficheurFormulaire($formulaire,$donneesFormulaire);
		$afficheurFormulaire->setRole($my_role);
		
		$afficheurFormulaire->injectHiddenField("id_d",$id_d);
		$afficheurFormulaire->injectHiddenField("form_type",$type);
		$afficheurFormulaire->injectHiddenField("id_e",$id_e);
		$afficheurFormulaire->injectHiddenField("action",$action);
		
		$last_action = $this->DocumentActionEntite->getLastActionNotModif($id_e, $id_d);
		
		$editable_content = $documentType->getAction()->getEditableContent($last_action);
		
		if ($editable_content && (!in_array($last_action,array("creation","modification")) || $editable_content)){
			$afficheurFormulaire->setEditableContent($editable_content);
		}
		
		$this->page_title="Edition d'un document « " . $documentType->getName() . " » ( " . $infoEntite['denomination'] . " ) ";
		
		$this->info = $info;
		$this->id_e = $id_e;
		$this->id_d = $id_d;
		$this->page = $page;
		$this->type = $type;
		$this->action = $action;
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
		
		$liste_collectivite = $this->roleUtilisateur->getEntiteWithSomeDroit($this->getId_u());
		
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
			$this->type_list = $this->getAllType($this->listDocument);
		}
		
		$this->infoEntite = $this->EntiteSQL->getInfo($id_e);
		$this->id_e = $id_e;
		$this->search = $search;
		$this->offset = $offset;
		$this->limit = $limit;
		
		
		$this->setNavigationInfo($id_e,"document/index.php?a=a");
		$this->page_title= "Liste des documents " . $this->infoEntite['denomination'] ;
		$this->template_milieu = "DocumentIndex"; 
		$this->renderDefault();
	}
	
	private function getAllType(array $listDocument){
		$type = array();
		foreach($listDocument as $doc){
			$type[$doc['type']] = $doc['type'];
			
		}
		return array_keys($type);
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
			$this->id_e_menu = $id_e;
		}
			
		
		$this->verifDroit($id_e, "$type:lecture");
		$this->infoEntite = $this->EntiteSQL->getInfo($id_e);
		
		$page_title = "Liste des documents " . $documentType->getName();
		if ($id_e){
			$page_title .= " pour " . $this->infoEntite['denomination'];
		}
		
		$this->page_title = $page_title;
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
		
		$this->listDocument = $this->DocumentActionEntite->getListDocument($id_e , $type , $offset, $limit,$search,$filtre ) ;
		$this->type_list = $this->getAllType($this->listDocument);
		
		
		$this->template_milieu = "DocumentList"; 
		$this->renderDefault();
	}
	
	public function searchDocument($is_date_iso = false){
		$recuperateur = new Recuperateur($_REQUEST);
		$this->id_e = $recuperateur->get('id_e',0);
		$this->type = $recuperateur->get('type');
		$this->lastEtat = $recuperateur->get('lastetat');
		$this->last_state_begin = $recuperateur->get('last_state_begin');
		$this->last_state_end = $recuperateur->get('last_state_end');
		$this->state_begin = $recuperateur->get('state_begin');
		$this->state_end = $recuperateur->get('state_end');
		
		if(! $is_date_iso){
			$this->last_state_begin_iso = getDateIso($this->last_state_begin );
			$this->last_state_end_iso = getDateIso($this->last_state_end);
			$this->state_begin_iso =  getDateIso($this->state_begin );
			$this->state_end_iso =    getDateIso($this->state_end );
		} else {
			$this->last_state_begin_iso = $this->last_state_begin;
			$this->last_state_end_iso = $this->last_state_end;
			$this->state_begin_iso =  $this->state_begin ;
			$this->state_end_iso =   $this->state_end;
		}
		
		if ( ! $this->id_e ){
			$this->LastError->setLastError("id_e est obligatoire");
			$this->redirect("");
		}
		$this->verifDroit($this->id_e, "entite:lecture");
		
		$this->allDroitEntite = $this->RoleUtilisateur->getAllDocumentLecture($this->getId_u(),$this->id_e);
		
		$this->etatTransit = $recuperateur->get('etatTransit');
		

		$this->tri =  $recuperateur->get('tri');
		$this->go = $recuperateur->get('go',0);
		$this->offset = $recuperateur->getInt('offset',0);
		$this->search = $recuperateur->get('search');
		
		$this->limit = 20;
		
		$allDroit = $this->RoleUtilisateur->getAllDroit($this->getId_u());		
		$this->arbre = $this->RoleUtilisateur->getArbreFille($this->getId_u(),"entite:lecture");
		
		$this->listeEtat = $this->DocumentTypeFactory->getActionByRole($allDroit);
		
		$this->documentActionEntite = $this->DocumentActionEntite;
		$this->documentTypeFactory = $this->DocumentTypeFactory;
		
		$this->my_id_e= $this->id_e;
		$this->listDocument = $this->DocumentActionEntite->getListBySearch($this->id_e,$this->type,
				$this->offset,$this->limit,$this->search,$this->lastEtat,$this->last_state_begin_iso,$this->last_state_end_iso,
				$this->tri,$this->allDroitEntite,$this->etatTransit,$this->state_begin_iso,$this->state_end_iso);	

		$this->type_list = $this->getAllType($this->listDocument);
	}
	
	public function searchAction(){				
		$this->searchDocument();
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
	
	
	private function validTraitementParLot($input){
		$recuperateur = new Recuperateur($input);
		$this->id_e = $recuperateur->get('id_e',0);
		$this->offset = $recuperateur->getInt('offset',0);
		$this->search = $recuperateur->get('search');
		$this->type = $recuperateur->get('type');
		$this->filtre = $recuperateur->get('filtre');
		$this->limit = 20;
		
		if (! $this->type){
			$this->redirect("/document/index.php?id_e={$this->id_e}");
		}
		if (!$this->id_e){
			$this->redirect("/document/index.php");
		}
		
		$this->id_e_menu = $this->id_e;
		$this->verifDroit($this->id_e, "{$this->type}:lecture");
		$this->infoEntite = $this->EntiteSQL->getInfo($this->id_e);
	}
	
	public function traitementLotAction(){
		$this->validTraitementParLot($_GET);
		$documentType = $this->DocumentTypeFactory->getFluxDocumentType($this->type);
		$page_title = "Traitement par lot pour les  documents " . $documentType->getName();
		$page_title .= " pour " . $this->infoEntite['denomination'];
		$this->page_title = $page_title;
		
	
		$this->documentTypeFactory = $this->DocumentTypeFactory;
		$this->setNavigationInfo($this->id_e,"document/list.php?type={$this->type}");
		$this->theAction = $documentType->getAction();
		
		$listDocument = $this->DocumentActionEntite->getListDocument($this->id_e , $this->type , $this->offset, $this->limit,$this->search,$this->filtre ) ;
		
		$all_action = array();
		foreach($listDocument as $i => $document){
			$listDocument[$i]['action_possible'] = $this->ActionPossible->getActionPossible($this->id_e,$this->Authentification->getId(),$document['id_d']);
			$all_action = array_merge($all_action,$listDocument[$i]['action_possible']);
		}
		$this->listDocument = $listDocument;
		$this->all_action = array_unique($all_action);
		$this->type_list = $this->getAllType($this->listDocument);		
		$this->template_milieu = "DocumentTraitementLot";
		$this->renderDefault();
	}
	
	public function confirmTraitementLotAction(){
		$this->validTraitementParLot($_GET);
		$documentType = $this->DocumentTypeFactory->getFluxDocumentType($this->type);
		$this->page_title = "Confirmation du traitement par lot pour les  documents " . $documentType->getName() ." pour " . $this->infoEntite['denomination'];
		
		$this->url_retour = "document/traitement-lot.php?id_e={$this->id_e}&type={$this->type}&search={$this->search}&filtre={$this->filtre}&offset={$this->offset}";
		
		$recuperateur = new Recuperateur($_GET);
		$this->action_selected = $recuperateur->get('action');
		$this->theAction = $documentType->getAction();
		
		$action_libelle = $this->theAction->getActionName($this->action_selected);
		
		$all_id_d = $recuperateur->get('id_d');
		if (! $all_id_d){
			$this->LastError->setLastError("Vous devez sélectionner au moins un document");
			$this->redirect($this->url_retour);
		}
		
		$error = "";
		foreach($all_id_d as $id_d){
			$infoDocument  = $this->DocumentActionEntite->getInfo($id_d,$this->id_e);
			if (! $this->ActionPossible->isActionPossible($this->id_e,$this->Authentification->getId(),$id_d,$this->action_selected)){
				$error .= "L'action « $action_libelle » n'est pas possible pour le document « {$infoDocument['titre']} »<br/>";
			}
			if ($this->ActionProgrammeeSQL->hasActionProgrammee($id_d,$this->id_e)) {
				$error .= "Il y a déjà une action programmée pour le document « {$infoDocument['titre']} »<br/>";
			}
			$listDocument[] = $infoDocument;
		}
		if ($error){
			$this->LastError->setLastError($error."<br/><br/>Aucune action n'a été executée");
			$this->redirect($this->url_retour);
		}
				
		$this->listDocument = $listDocument;
		$this->template_milieu = "DocumentConfirmTraitementLot";
		$this->renderDefault();
	}
	
	public function doTraitementLotAction(){
		$this->validTraitementParLot($_POST);
		$recuperateur = new Recuperateur($_POST);
		$action_selected = $recuperateur->get('action');
		$all_id_d = $recuperateur->get('id_d');
		$documentType = $this->DocumentTypeFactory->getFluxDocumentType($this->type);
		
		$action_libelle = $documentType->getAction()->getActionName($action_selected);
		
		$url_retour = "document/traitement-lot.php?id_e={$this->id_e}&type={$this->type}&search={$this->search}&filtre={$this->filtre}&offset={$this->offset}";
		
		$error = "";
		$message ="";
		foreach($all_id_d as $id_d){
			$infoDocument  = $this->DocumentActionEntite->getInfo($id_d,$this->id_e);
			if (! $this->ActionPossible->isActionPossible($this->id_e,$this->Authentification->getId(),$id_d,$action_selected)){
				$error .= "L'action « $action_libelle » n'est pas possible pour le document « {$infoDocument['titre']} »<br/>";
			} 
			if ($this->ActionProgrammeeSQL->hasActionProgrammee($id_d,$this->id_e)) {
				$error .= "Il y a déjà une action programmée pour le document « {$infoDocument['titre']} »<br/>";
			}
			$listDocument[] = $infoDocument;
			$message .= "L'action « $action_libelle » est programmé pour le document « {$infoDocument['titre']} »<br/>";	
		
		}
		if ($error){
			$this->LastError->setLastError($error."<br/><br/>Aucune action n'a été executée");
			$this->redirect($this->url_retour);
		}
		
		foreach($all_id_d as $id_d){
			$this->ActionProgrammeeSQL->add($id_d,$this->id_e,$this->Authentification->getId(),$action_selected);
			$this->Journal->add(Journal::DOCUMENT_TRAITEMENT_LOT,$this->id_e,$id_d,$action_selected,"programmation dans le cadre d'un traitement par lot");
		}
		
		$this->LastMessage->setLastMessage($message);
		$url_retour = "document/list.php?id_e={$this->id_e}&type={$this->type}&search={$this->search}&filtre={$this->filtre}&offset={$this->offset}";
		$this->redirect($url_retour);
	}
	
	private function doOneAction($id_d,$id_e,$id_u,$action){

		$info = $this->Document->getInfo($id_d);
		if (! $this->RoleUtilisateur->hasDroit($id_u,"{$info['type']}:edition",$id_e)){
				throw new Exception("Vous n'avez pas les droits suffisants pour executer l'action");
		}
						
		if ( ! $this->ActionPossible->isActionPossible($id_e,$id_u,$id_d,$action)) {
			throw new Exception("L'action « $action »  n'est pas permise : " .$this->ActionPossible->getLastBadRule());
		}
			
		$result = $this->ActionExecutorFactory->executeOnDocument($id_e,$id_u,$id_d,$action,array(), true,array());
		$message = $this->objectInstancier->ActionExecutorFactory->getLastMessage();
			
		if (! $result){
			throw new Exception($message);
		} 
		return true;
	}
	
	public function doActionProgrammee(){
		$all_action = $this->ActionProgrammeeSQL->getAll();
		foreach($all_action as $actionInfo){
			try{
				$this->doOneAction($actionInfo['id_d'],$actionInfo['id_e'],$actionInfo['id_u'],$actionInfo['action']);
			} catch (Exception $e){
				$info = $this->Document->getInfo($actionInfo['id_d']);
				$this->NotificationMail->notify($actionInfo['id_e'],$actionInfo['id_d'],$actionInfo['action'],$info['type'],"Echec de l'execution de l'action dans la cadre d'un traitement par lot : ".$e->getMessage());
			}
			$this->ActionProgrammeeSQL->delete($actionInfo['id_d'],$actionInfo['id_e']);
		}
		
		
	}
	
	
}