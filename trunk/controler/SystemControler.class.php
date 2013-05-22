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
			case 0:
			default: $this->actionAutoAction(); break;
			
		}
		
		
		$this->onglet_tab = array("Action automatique","Environnement système","Flux");
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
		$this->all_flux = $this->FluxDefinitionFiles->getAll();
		$this->onglet_content = "SystemFlux";
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
		
		$action = $documentType->getAction();
		$action_list = $action->getAll();
		sort($action_list);
		foreach($action_list as $action_name){
			$class_name = $action->getActionClass($action_name);
			$all_action[] = array(
				'name'=> $action_name,
				'class' => $class_name,
				'path' => $this->ActionExecutorFactory->getFluxActionPath($id,$class_name),
			); 	
		}
		$this->all_action = $all_action;
		$this->page_title = "Détail du flux « $name »";
		$this->template_milieu = "SystemFluxDetail";
		$this->renderDefault();
	}
	
}