<?php

class ConnecteurException extends Exception {}


abstract class Connecteur {
	
	protected $lastError;
	
	abstract function setConnecteurConfig(DonneesFormulaire $donnesFormulaire);
		
	public function getLastError(){
		return $this->lastError;
	}
	
	
}