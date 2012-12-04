<?php

class ObjectInstancier {

	private $objects;
		
	public function __construct(){
		$this->objects = array('ObjectInstancier' => $this);
	}
	
	public function __get($name){
		if (! isset($this->objects[$name])){
			$this->objects[$name] =  $this->newInstance($name);	
		}
		return $this->objects[$name];
	}
	
	public function __set($name,$value){
		$this->objects[$name] = $value;
	}

	public function newInstance($className){
		try {
		$reflexionClass = new ReflectionClass($className);
		if (! $reflexionClass->hasMethod('__construct')){
			return $reflexionClass->newInstance();
		}
		$constructor = $reflexionClass->getMethod('__construct');
        $allParameters = $constructor->getParameters();
        $param = $this->bindParameters($allParameters);        
        return $reflexionClass->newInstanceArgs($param);
		} catch (Exception $e){
			throw new Exception("En essayant d'inclure $className",0,$e);
		}
	}
	
	private function bindParameters(array $allParameters){
		$param = array();
		foreach($allParameters as $parameters){  
        	$param_name = $parameters->getClass() ? $parameters->getClass()->name : $parameters->name;
        	$bind_value = $this->$param_name;
        	if (! $bind_value ) {
        		if ($parameters->isOptional()){
        			return $param;
        		}
        		throw new Exception("Impossible d'instancier $className car le paramètre {$parameters->name} est manquant");
        	}
        	$param[] = $bind_value;
        }
        return $param;
	}
}