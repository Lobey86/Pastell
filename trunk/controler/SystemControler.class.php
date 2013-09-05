<?php
class SystemControler extends PastellControler {
	
	public function indexAction(){
		$this->verifDroit(0,"system:lecture");

		$recuperateur=new Recuperateur($_GET);
		$page_number = $recuperateur->getInt('page_number');
		
		switch($page_number){
		
			case 1 : 
				$this->environnementAction(); break;	
			case 2:
				$this->fluxAction(); break;	
			case 3:
				$this->fluxDefAction(); break;
			case 0:
			default: $this->actionAutoAction(); break;
			
		}
		
		$this->onglet_tab = array("Action automatique","Environnement système","Flux","Définition des flux");
		$this->page_number = $page_number;
		$this->template_milieu = "SystemIndex";
		$this->page_title = "Environnement système";
		$this->renderDefault();
	}
	
	private function actionAutoAction(){
		$recuperateur=new Recuperateur($_GET);
		$offset = $recuperateur->getInt('offset');
		$this->last_upstart =  $this->LastUpstart->getLastMtime();
		$this->all_log = $this->ActionAutoLogSQL->getLog($offset);
		$this->offset = $offset;
		$this->limit = ActionAutoLogSQL::LIMIT_AFFICHE;
		$this->count =  $this->ActionAutoLogSQL->countLog();
		$this->onglet_content = "SystemActionAutoList";
	}
	
	private function environnementAction(){
		$this->checkExtension = $this->VerifEnvironnement->checkExtension();
		$this->checkPHP = $this->VerifEnvironnement->checkPHP();
		$this->checkWorkspace = $this->VerifEnvironnement->checkWorkspace();
		
		$this->valeurMinimum = array(
			"PHP" => $this->checkPHP['min_value'],
			"OpenSSL" => '1.0.0a',
		);
		$cmd =  OPENSSL_PATH . " version";
		$openssl_version = `$cmd`;
		$this->valeurReel = array('OpenSSL' =>  $openssl_version, 'PHP' => $this->checkPHP['environnement_value']); 

		$this->onglet_content = "SystemEnvironnement";
	}
	
	private function fluxAction(){
		$all_flux = array();
		$all_connecteur_type = $this->ConnecteurDefinitionFiles->getAllType();
		$all_type_entite = array_keys(Entite::getAllType());
		foreach($this->FluxDefinitionFiles->getAll() as $id_flux => $flux){
			$documentType = $this->DocumentTypeFactory->getFluxDocumentType($id_flux);
			$all_flux[$id_flux]['nom'] = $documentType->getName();
			$all_flux[$id_flux]['type'] = $documentType->getType();
			$all_flux[$id_flux]['is_valide'] = $this->DocumentTypeValidation->validate($id_flux,
														$this->DocumentTypeFactory->getDocumentTypeArray($id_flux),
														$all_connecteur_type,
														$all_type_entite);
		}
		$this->all_flux = $all_flux;
		$this->onglet_content = "SystemFlux";
	}
	
	public function fluxDefAction(){
		$this->flux_definition = $this->DocumentTypeValidation->getModuleDefinition();
		$this->onglet_content = "SystemFluxDef";
	}
	
	public function messageAction(){
		$recuperateur=new Recuperateur($_GET);
		$id_e = $recuperateur->getInt('id_e');
		$id_d = $recuperateur->get('id_d');
		$this->all_message = $this->ActionAutoLogSQL->getMessage($id_e,$id_d);
		$this->template_milieu = "SystemActionAutoMessage";
		$this->page_title = "Environnement système";
	 			
		$this->renderDefault();
	}
	
	public function fluxDetailAction(){
		$recuperateur=new Recuperateur($_GET);
		$id = $recuperateur->get('id');		
		$documentType = $this->DocumentTypeFactory->getFluxDocumentType($id);
		
		$name = $documentType->getName();
		$this->description = $documentType->getDescription();
		$all_action = array();
		$action = $documentType->getAction();
		$action_list = $action->getAll();
		sort($action_list);
		foreach($action_list as $action_name){
			$class_name = $action->getActionClass($action_name);
			$all_action[] = array(
				'id'=> $action_name,
				'name' => $action->getActionName($action_name),
				'do_name' => $action->getDoActionName($action_name),
				'class' => $class_name,
				'path' => $this->ActionExecutorFactory->getFluxActionPath($id,$class_name),
			); 	
		}
		$this->all_action = $all_action;
		
		$all_connecteur_type = $this->ConnecteurDefinitionFiles->getAllType();
		$all_type_entite = array_keys(Entite::getAllType());
		
		
		$this->document_type_is_validate = $this->DocumentTypeValidation->validate($id,
			$this->DocumentTypeFactory->getDocumentTypeArray($id),$all_connecteur_type,$all_type_entite);
		$this->validation_error =  $this->DocumentTypeValidation->getLastError();
		$this->page_title = "Détail du flux « $name »";
		$this->template_milieu = "SystemFluxDetail";
		$this->renderDefault();
	}
	
}