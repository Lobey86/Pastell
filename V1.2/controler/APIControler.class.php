<?php
class APIControler extends PastellControler {
	
	public function indexAction(){
		$this->functions_list = $this->APIDefinition->getFunctions();
		$this->page_title = "API Pastell";
		$this->template_milieu = "APIIndex"; 
		$this->renderDefault();
	}
	
}