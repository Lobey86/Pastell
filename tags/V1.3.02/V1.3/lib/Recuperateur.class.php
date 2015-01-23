<?php 
class Recuperateur {
	
	private $tableauInput;
	
	public function __construct($tableauInput = null ){
		if (! $tableauInput){
			$tableauInput = $_REQUEST;
		}
		$this->tableauInput = $tableauInput;
	}
	
	public function getInt($name,$default = 0){
		return $this->doSomethingOnValueOrArray('intval',$this->get($name,$default));
	}
	
	public function getNoTrim($name,$default = false){
		if ( empty($this->tableauInput[$name])) {
			return $default;
		}
		return $this->tableauInput[$name];
	}
	
	public function get($name,$default = null){
		if ( empty($this->tableauInput[$name])) {
			return $default;
		}
		
		$value = $this->tableauInput[$name];		
		if(is_array($value)){
			foreach($value as $i => $v){
				$v = str_replace("&#8217;","'",$v);
				$value[$i] = str_replace("&#8211;","-",$v);
			}
		} else {
			$value = str_replace("&#8217;","'",$value);
			$value = str_replace("&#8211;","-",$value); //Attention, il faudrait mettre un "em dash" qui n'existe pas en iso-8859-1
		}
		
		return $this->doSomethingOnValueOrArray("trim",$value);
	}
	
	private function doSomethingOnValueOrArray($something,$valueOrArray){
		if (is_array($valueOrArray)){
			return array_map($something,$valueOrArray);
		} 
		return $something($valueOrArray);
	}
	
	public function getAll(){
		$result = array();
		foreach($this->tableauInput as $name => $item){
			$result[$name] = $this->get($name);
		}
		return $result;
	}
	
}
