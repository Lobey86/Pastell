<?php
abstract class Connecteur {
	
	protected $lastError;
	
	public function getLastError(){
		return $this->lastError;
	}
	
	
}