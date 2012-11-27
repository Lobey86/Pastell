<?php
abstract class Connecteur {
	
	protected $lastError;
	private $config_properties;
	
	public function __construct(array $config_properties){
		$this->config_properties=$config_properties;
	}
		
	public function getLastError(){
		return $this->lastError;
	}
	
	
}