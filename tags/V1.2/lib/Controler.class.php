<?php


class LastErrorException extends Exception {}
class LastMessageException extends Exception {}


class Controler {

	private $objectInstancier;
	private $selectedView;
	private $viewParameter;
	protected $lastError;
	private $dont_redirect;
	
	public function __construct(ObjectInstancier $objectInstancier){
		$this->objectInstancier = $objectInstancier;
		$this->viewParameter = array();
	}
	
	public function setDontRedirect($dont_redirect){
		$this->dont_redirect = $dont_redirect;
	}
	
	public function isDontRedirect(){
		return $this->dont_redirect;
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
		$this->doRedirect($this->objectInstancier->site_index);
	}
	
	public function redirect($to = ""){
		$this->doRedirect(SITE_BASE."$to");
	}
	
	private function doRedirect($url){
		if ($this->isDontRedirect()){
			$error = $this->LastError->getLastError();
			if ($error){
				throw new LastErrorException("Redirection vers $url : $error");
			} else {
				$message = $this->LastMessage->getLastMessage();
				throw new LastMessageException("Rediection vers $url: $message");
			}
		}
		header("Location: $url");
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