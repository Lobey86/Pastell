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
	
	public function getViewParameter(){
		return $this->viewParameter;
	}
	
	public function selectView($view){
		$this->selectedView = $view;
	}
	
	public function getSelectedView(){
		return $this->selectedView;
	}
	
	public function exitIfNotConnected(){
		if (! $this->connexion->isConnected()){
			$this->LastMessage->setLastError("Vous devez vous connecter pour utiliser cette fonctionnalité");
			$this->redirect("Login","index");
		}
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
		$template_milieu = $this->getSelectedView();
		if (! $this->Gabarit->templateExists($template_milieu)){
			header("HTTP/1.0 404 Not Found");
			echo "Cette page n'existe pas";
			return;
		} 
		
		$this->template_milieu = $template_milieu;
		$this->Gabarit->setParameters($this->getViewParameter());
		$this->Gabarit->render("Page");
	}
	
}