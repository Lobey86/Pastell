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

	
	public function suivantPrecedent($offset,$limit,$nb_total,$link = null,$message=null) {
		if (! $message){
			$message = 'Position %1$s à %2$s sur %3$s';
		}
		
		if (! $link){
			$link = $_SERVER['PHP_SELF'];
		}
		if ( strstr($link,"?")){
			 $link = $link."&";
		} else {
			 $link = $link."?";
		}
		include("{$this->template_path}/SuivantPrecedent.php");
	}
}