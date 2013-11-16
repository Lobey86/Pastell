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
				$value[$i] = str_replace("&#8217;","'",$v);
			}
		} else {
			$value = str_replace("&#8217;","'",$value);
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
