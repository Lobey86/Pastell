<?php
class SystemControler extends PastellControler {
	
	
	
	
	public function indexAction(){
		$recuperateur=new Recuperateur($_GET);
		$page_number = $recuperateur->getInt('page_number');
		$this->ensureDroit("system:lecture", 0);
		
		if ($page_number == 0){
			$this->actionAutoAction();
		} else {
			$this->environnementAction();
		}
		
		$this->onglet_tab = array("Action automatique","Environnement système");
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
	
	public function messageAction(){
		$recuperateur=new Recuperateur($_GET);
		$id_e = $recuperateur->getInt('id_e');
		$id_d = $recuperateur->get('id_d');
		$this->all_message = $this->ActionAutoLogSQL->getMessage($id_e,$id_d);
		$this->template_milieu = "SystemActionAutoMessage";
		$this->page_title = "Environnement système";
		
		$this->renderDefault();
	}
	
}