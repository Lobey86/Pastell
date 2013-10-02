<?php
abstract class ChoiceActionExecutor extends ActionExecutor {

	private $viewParameter;
	protected $field;
		
	public function __construct(ObjectInstancier $objectInstancier){
		parent::__construct($objectInstancier);
		$this->viewParameter = array();
	}
	
	public function setField($field){
		$this->field = $field;
	}
	
	public function __set($key,$value){
		$this->viewParameter[$key] = $value;
		$this->$key  = $value;
	}
	
	public function getViewParameter(){
		$this->viewParameter['id_d'] = $this->id_d;
		$this->viewParameter['id_e'] = $this->id_e;
		$this->viewParameter['id_ce'] = $this->id_ce;	
		$this->viewParameter['field'] = $this->field;	
		return $this->viewParameter;
	}
	
	public function renderPage($page_title,$template){		
		$this->page_title = $page_title;
		$this->template_milieu = $template;
		$this->objectInstancier->PastellControler->setAllViewParameter($this->getViewParameter());
		$this->objectInstancier->PastellControler->renderDefault();
	}
	
	public function redirectToFormulaire(){
		header("Location: edition.php?id_d={$this->id_d}&id_e={$this->id_e}&page={$this->page}");
	}
	
	public function redirectToConnecteurFormulaire(){
		header("Location: edition-modif.php?id_ce={$this->id_ce}");
	}
	
	public function isEnabled(){
		return true;
	}
	
	abstract public function display();
	
	abstract public function displayAPI();
	
}