<?php


class Action {
	
	const CREATION = "creation";
	const MODIFICATION = "modification";
	
	private $action;
	
	public function __construct(array $action){
		$this->action = $action;
	}
	
	public function getAll(){
		return $this->action;
	}
	
	public function getActionRule($action){
		if (! isset ($this->action[$action])){
			return array();
		}
		return $this->action[$action]['rule'];
	}
	
	public function getActionScript($action){
		if (! isset($this->action[$action]['action-script'])){
			return "default.php";
		}
		return $this->action[$action]['action-script'];
	}
	
	public function getActionName($action){
		if (! isset($this->action[$action]['name'])){
			return $action;
		}
		return $this->action[$action]['name'];
	}
	
	
}