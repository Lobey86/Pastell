<?php
class Gabarit {
	
	private $viewParameter;
	private $objectInstancier;
	
	public function __construct(ObjectInstancier $objectInstancier){
		$this->viewParameter = array();
		$this->objectInstancier = $objectInstancier;
	}
	
	public function __set($key,$value){
		$this->viewParameter[$key] = $value;
	}
	
	public function setViewParameter($key,$value){
		$this->viewParameter[$key] = $value;
	}
	
	public function setParameters(array $parameter){
		$this->viewParameter = array_merge($this->viewParameter,$parameter); 
	}
	
	public function render($template){		
		foreach($this->viewParameter as $key => $value){
			$$key = $value;
		}		
		include("{$this->template_path}/$template.php");
	}
	
	public function templateExists($template){
		return file_exists("{$this->template_path}/$template.php");
	}
	
	public function __get($key){
		if (isset($this->viewParameter[$key])){
			return $this->viewParameter[$key];
		}
		return $this->objectInstancier->$key;
	}
	
	public function renderPage($template){
		$this->authentification = $this->objectInstancier->Authentification;
		$this->roleUtilisateur = $this->objectInstancier->roleUtilisateur;
		$this->sqlQuery = $this->sqlQuery;
		$this->setViewParameter("objectInstancier",$this->objectInstancier);
		$this->versionning = $this->objectInstancier->Versionning;
		$this->timer = $this->objectInstancier->Timer;
		foreach($this->viewParameter as $key => $value){
			$$key = $value;
		}	
		include( PASTELL_PATH ."/include/haut.php");
		include("{$this->template_path}/$template.php");
		include( PASTELL_PATH ."/include/bas.php");
	}
}