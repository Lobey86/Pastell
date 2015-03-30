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
		$this->donneesFormulaire->getFormulaire()->setTabNumber($page);
		
		$this->actionPossible = $this->ActionPossible;
		$this->theAction = $documentType->getAction();
		$this->documentEntite = $this->DocumentEntite;
		$this->my_role = $this->documentEntite->getRole($id_e,$id_d);
		$this->documentEmail = $this->DocumentEmail;
		$this->documentActionEntite = $this->DocumentActionEntite;
		
		$this->next_action_automatique =  $this->theAction->getActionAutomatique($true_last_action);
		$this->droit_erreur_fatale = $this->RoleUtilisateur->hasDroit($this->getId_u(),$info_document['type'].":edition",0);
		
		$this->page_title =  $info_document['titre'] . " (".$documentType->getName().")";
		
		if ($documentType->isAfficheOneTab()){
			$this->fieldDataList = $this->donneesFormulaire->getFieldDataListAllOnglet($this->my_role); 
		} else {
			$this->fieldDataList = $this->donneesFormulaire->getFieldDataList($this->my_role,$page);
		}
		
		
		$this->recuperation_fichier_url = "document/recuperation-fichier.php?id_d=$id_d&id_e=$id_e";
		
		
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
		
		$infoEntite = $this->EntiteSQL->getInfo($id_e);
		
		$donneesFormulaire = $this->DonneesFormulaireFactory->get($id_d,$type);
		
		$formulaire = $donneesFormulaire->getFormulaire();
		if (! $formulaire->tabNumberExists($page)){
			$page = 0;
		}
		
		
		$this->inject = array('id_e'=>$id_e,'id_d'=>$id_d,'form_type'=>$type,'action'=>$action,'id_ce'=>'');
		
		
		$last_action = $this->DocumentActionEntite->getLastActionNotModif($id_e, $id_d);
		
		$editable_content = $documentType->getAction()->getEditableContent($last_action);
		
		if ( (! in_array($last_action,array("creation","modification"))) || $editable_content){
			if ($editable_content){
				$donneesFormulaire->setEditableContent($editable_content);
			}
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

		$this->action_url = "document/edition-controler.php";
		$this->recuperation_fichier_url = "document/recuperation-fichier.php?id_d=$id_d&id_e=$id_e";
		$this->suppression_fichier_url = "document/supprimer-fichier.php?id_d=$id_d&id_e=$id_e&page=$page&action=$action";
		$this->externalDataURL = "document/external-data.php" ;
		
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
		
		$this->tri =  $recuperateur->get('tri','date_dernier_etat');
		$this->sens_tri = $recuperateur->get('sens_tri','DESC');
		
		$this->url_tri = false;
		
		
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
		
		$this->champs_affiches = array('titre'=>'Objet','type'=>'Type','entite'=>'Entité','dernier_etat'=>'Dernier état','date_dernier_etat'=>'Date');
		
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
			$this->type_e_menu = $type;
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
		
		$this->tri =  $recuperateur->get('tri','date_dernier_etat');
		$this->sens_tri = $recuperateur->get('sens_tri','DESC');
		
		
		$this->documentTypeFactory = $this->DocumentTypeFactory;
		$this->setNavigationInfo($id_e,"document/list.php?type=$type");
		
		$this->champs_affiches = $documentType->getChampsAffiches();
		
		
		$this->allDroitEntite = $this->RoleUtilisateur->getAllDocumentLecture($this->getId_u(),$this->id_e);
		
		$this->indexedFieldsList = $documentType->getFormulaire()->getIndexedFields();
		$indexedFieldValue = array();
		foreach($this->indexedFieldsList as $indexField => $indexLibelle){
			$indexedFieldValue[$indexField] = $recuperateur->get($indexField);
		}
		
		$this->listDocument = $this->DocumentActionEntite->getListBySearch($id_e,$type,
				$offset,$limit,$search,$filtre,false,false,$this->tri,
				$this->allDroitEntite,false,false,false,$indexedFieldValue,$this->sens_tri
		);
		
		
		$this->url_tri = "document/list.php?id_e=$id_e&type=$type&search=$search&filtre=$filtre";
		
		$this->type_list = $this->getAllType($this->listDocument);
		
		$this->template_milieu = "DocumentList"; 
		$this->renderDefault();
	}
	
	public function searchDocument($is_date_iso = false,$is_api=false){
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
			$error_message = "id_e est obligatoire";
			if ($is_api){
				throw new Exception($error_message);
			}
			$this->LastError->setLastError($error_message);
			$this->redirect("");
		}
		$this->verifDroit($this->id_e, "entite:lecture");
		
		$this->allDroitEntite = $this->RoleUtilisateur->getAllDocumentLecture($this->getId_u(),$this->id_e);
		
		$this->etatTransit = $recuperateur->get('etatTransit');
		

		$this->tri =  $recuperateur->get('tri','date_dernier_etat');
		$this->sens_tri = $recuperateur->get('sens_tri','DESC');
		$this->go = $recuperateur->get('go',0);
		$this->offset = $recuperateur->getInt('offset',0);
		$this->search = $recuperateur->get('search');
		
		$this->limit = 20;
		
		$indexedFieldValue = array();
		if ($this->type) {
			$documentType = $this->DocumentTypeFactory->getFluxDocumentType($this->type);
			$this->indexedFieldsList = $documentType->getFormulaire()->getIndexedFields();
			
			foreach($this->indexedFieldsList as $indexField => $indexLibelle){
				$indexedFieldValue[$indexField] = $recuperateur->get($indexField);
				if ($documentType->getFormulaire()->getField($indexField)->getType() == 'date'){
					$indexedFieldValue[$indexField] = date_fr_to_iso($recuperateur->get($indexField));
				}
			}
			$this->champs_affiches = $documentType->getChampsAffiches();
		} else {
			$this->champs_affiches = array('titre'=>'Objet','type'=>'Type','entite'=>'Entité','dernier_etat'=>'Dernier état','date_dernier_etat'=>'Date');
			$this->indexedFieldsList = array();
		}
				
		$this->indexedFieldValue = $indexedFieldValue;
		
		
		$allDroit = $this->RoleUtilisateur->getAllDroit($this->getId_u());		
		$this->listeEtat = $this->DocumentTypeFactory->getActionByRole($allDroit);
		
		$this->documentActionEntite = $this->DocumentActionEntite;
		$this->documentTypeFactory = $this->DocumentTypeFactory;
		
		$this->my_id_e= $this->id_e;
		$this->listDocument = $this->DocumentActionEntite->getListBySearch($this->id_e,$this->type,
				$this->offset,$this->limit,$this->search,$this->lastEtat,$this->last_state_begin_iso,$this->last_state_end_iso,
				$this->tri,$this->allDroitEntite,$this->etatTransit,$this->state_begin_iso,$this->state_end_iso,
				$indexedFieldValue,$this->sens_tri
		);	

		$url_tri = "document/search.php?id_e={$this->id_e}&search={$this->search}&type={$this->type}&lastetat={$this->lastEtat}".
						"&last_state_begin={$this->last_state_begin_iso}&last_state_end={$this->last_state_end_iso}&etatTransit={$this->etatTransit}".
						"&state_begin={$this->state_begin_iso}&state_end={$this->state_end_iso}";

		if ($this->type){
			foreach($indexedFieldValue as $indexName => $indexValue){
				$url_tri.="&".urlencode($indexName)."=".urlencode($indexValue);
			}
		}
		
		
		$this->url_tri = $url_tri;
		$this->type_list = $this->getAllType($this->listDocument);
	}
	
	public function exportAction(){
		$recuperateur = new Recuperateur($_GET);
		$id_e = $recuperateur->get('id_e',0);
		$type = $recuperateur->get('type');
		$search = $recuperateur->get('search');
		
		$lastEtat = $recuperateur->get('lastetat');
		$last_state_begin = $recuperateur->get('last_state_begin');
		$last_state_end = $recuperateur->get('last_state_end');
		
		$last_state_begin_iso = getDateIso($last_state_begin);
		$last_state_end_iso = getDateIso($last_state_end);
		
		$etatTransit = $recuperateur->get('etatTransit');
		$state_begin =  $recuperateur->get('state_begin');
		$state_end =  $recuperateur->get('state_end');
		$tri =  $recuperateur->get('tri');
		$sens_tri = $recuperateur->get('sens_tri');
		
		$offset = 0;

		$allDroitEntite = $this->RoleUtilisateur->getAllDocumentLecture($this->Authentification->getId(),$id_e);
		
		
		$indexedFieldValue = array();
		if ($type) {
			$documentType = $this->DocumentTypeFactory->getFluxDocumentType($type);
			$indexedFieldsList = $documentType->getFormulaire()->getIndexedFields();
			foreach($indexedFieldsList as $indexField => $indexLibelle){
				$indexedFieldValue[$indexField]=$recuperateur->get($indexField);
			}
			$champs_affiches = $documentType->getChampsAffiches();
		} else {
			$champs_affiches = array('titre'=>'Objet','type'=>'Type','entite'=>'Entité','dernier_etat'=>'Dernier état','date_dernier_etat'=>'Date');
			$indexedFieldsList = array();
				
		}
		
		
		$limit = $this->DocumentActionEntite->getNbDocumentBySearch($id_e,$type,$search,$lastEtat,$last_state_begin_iso,$last_state_end_iso,$allDroitEntite,$etatTransit,$state_begin,$state_end,$indexedFieldValue);
		$listDocument = $this->DocumentActionEntite->getListBySearch($id_e,$type,$offset,$limit,$search,$lastEtat,$last_state_begin_iso,$last_state_end_iso,$tri,$allDroitEntite,$etatTransit,$state_begin,$state_end,$indexedFieldValue,$sens_tri);
		
		$line = array("ENTITE","ID_D","TYPE","TITRE","DERNIERE ACTION","DATE DERNIERE ACTION");
		foreach($indexedFieldsList as $indexField=>$indexLibelle){
			$line[] = $indexLibelle;
		}
		$result = array($line);
		foreach($listDocument as $i => $document){
			 $line = array(
					$document['denomination'],
					$document['id_d'],
			 		$document['type'],
					$document['titre'],
					$document['last_action'],
					$document['last_action_date'],
						
			);
			foreach($indexedFieldsList as $indexField=>$indexLibelle){
				$line[] = $this->DocumentIndexSQL->get($document['id_d'],$indexField);
			}
			$result[] = $line;
		}
	
		$this->CSVoutput->sendAttachment("pastell-export-$id_e-$type-$search-$lastEtat-$tri.csv",$result);
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
		
		$this->id_e_menu = $this->id_e;
		$this->type_e_menu = $this->type;
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
			$listDocument[$i]['action_possible'] =  $this->ActionPossible->getActionPossibleLot($this->id_e,$this->Authentification->getId(),$document['id_d']);
			$all_action = array_merge($all_action,$listDocument[$i]['action_possible']);
		}
		$this->listDocument = $listDocument;
		
		$all_action = array_unique($all_action);
		
		$this->all_action = $all_action; 
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
		
		$action_libelle = $documentType->getAction()->getDoActionName($action_selected);
		
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
			$message .= "L'action « $action_libelle » est programmée pour le document « {$infoDocument['titre']} »<br/>";	
		
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
	
	public function reindex($document_type,$field_name,$offset=0,$limit=-1){
		if (! $this->DocumentTypeFactory->isTypePresent($document_type)){
			echo "[ERREUR] Le type de document $document_type n'existe pas sur cette plateforme.\n";
			return;
		}
		$documentType = $this->DocumentTypeFactory->getFluxDocumentType($document_type);
		$formulaire = $documentType->getFormulaire();
		
		
		$field = $formulaire->getField($field_name);
		if (! $field){
			echo "[ERREUR] Le champs $field_name n'existe pas pour le type de document $document_type\n";
			return;
		}
		if (! $field->isIndexed()){
			echo "[ERREUR] Le champs $document_type:$field_name n'est pas indexé\n";
			return;
		}

		$document_list = $this->Document->getAllByType($document_type);
        if ($limit > 0){
            $document_list = array_slice($document_list,$offset,$limit);
        }

        foreach($document_list as $document_info){ 
			echo "Réindexation du document {$document_info['titre']} ({$document_info['id_d']})\n";
			$documentIndexor = new DocumentIndexor($this->DocumentIndexSQL, $document_info['id_d']);
			$donneesFormulaire = $this->DonneesFormulaireFactory->get($document_info['id_d']);
			$fieldData = $donneesFormulaire->getFieldData($field_name);
			
			$documentIndexor->index($field_name, $fieldData->getValueForIndex());
		}
	}
	
	public function fixModuleChamps($document_type,$old_field_name,$new_field_name){
		foreach($this->Document->getAllByType($document_type) as $document_info){
			$donneesFormulaire = $this->DonneesFormulaireFactory->get($document_info['id_d']);
			$value = $donneesFormulaire->get($old_field_name);
			$donneesFormulaire->setData($new_field_name,$value);
			$donneesFormulaire->deleteField($old_field_name);
			
			echo $document_info['id_d'] ." : OK\n";
		}
	}
	
	public function visionneuseAction(){
		$recuperateur = new Recuperateur($_GET);
		$id_d = $recuperateur->get('id_d');
		$id_e = $recuperateur->getInt('id_e');
		$field = $recuperateur->get('field');
		$num = $recuperateur->getInt('num',0);
		
		$info_document = $this->verifDroitLecture($id_e, $id_d);
		
		$this->VisionneuseFactory->display($id_d,$field,$num);
	}
	
	
}