<?php
class Controler {

	private $objectInstancier;
	private $selectedView;
	private $viewParameter;
	protected $lastError;
	
	public function __construct(ObjectInstancier $objectInstancier){
		$this->objectInstancier = $objectInstancier;
		$this->viewParameter = array();
	}
	
	public function getLastError(){
		return $this->lastError;
	}

	public function __get($key){
		if (isset($this->$key)){
			return $this->$key;
		}
		return $this->objectInstancier->$key;
	}

	public function __set($key,$value){
		$this->viewParameter[$key] = $value;
		$this->$key  = $value;
	}
	
	public function setViewParameter($key,$value){
		$this->viewParameter[$key] = $value;
		$this->$key  = $value;
	}
	
	public function setAllViewParameter(array $viewParameter){
		$this->viewParameter = $viewParameter;
	}
	
	public function getViewParameter(){
		return $this->viewParameter;
	}
	
	public function exitToIndex(){
		header("Location: ".$this->objectInstancier->site_index);
		exit;
	}
	
	public function redirect($to = ""){
		header("Location: ".SITE_BASE."$to");
		exit;
	}

	public function renderDefault(){
		$template_milieu = $this->viewParameter['template_milieu'];
		$this->Gabarit->setParameters($this->getViewParameter());
		$this->Gabarit->render("Page");
	}
	
	public function render($template){
		$this->Gabarit->setParameters($this->getViewParameter());
		$this->Gabarit->render($template);
	}
}