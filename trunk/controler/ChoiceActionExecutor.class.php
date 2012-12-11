<?php
abstract class ChoiceActionExecutor extends ActionExecutor {

	private $viewParameter;
		
	public function __construct(ObjectInstancier $objectInstancier){
		parent::__construct($objectInstancier);
		$this->viewParameter = array();
	}
	
	public function __set($key,$value){
		$this->viewParameter[$key] = $value;
		$this->$key  = $value;
	}
	
	public function getViewParameter(){
		$this->viewParameter['id_d'] = $this->id_d;
		$this->viewParameter['id_e'] = $this->id_e;
		return $this->viewParameter;
	}
	
	public function renderPage($page_title,$template){		
		$this->page_title = $page_title;
		$this->objectInstancier->Gabarit->setParameters($this->getViewParameter());
				
		$this->objectInstancier->Gabarit->renderPage($template);		
		
	}
	
	public function redirectToFormulaire(){
		header("Location: edition.php?id_d={$this->id_d}&id_e={$this->id_e}&page={$this->page}");
	}
	
	abstract public function display();
	
	abstract public function displayAPI();
	
}