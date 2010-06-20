<?php 
require_once("ZLog.class.php");

class URLLoader {
	
	const DEFAULT_TIMEOUT = 3;
	
	private $url;
	private $error;
	private $time;
	private $timeout;
	private $size;
	private $followLocation;
	private $zLog;
	
	public function __construct(){
		$this->setTimeout(self::DEFAULT_TIMEOUT);
		$this->setFollowLocation(false);
	}	
	
	public function setTimeout($nb_secondes){
		$this->timeout = $nb_secondes;
	}
	
	public function setFollowLocation($bool){
		$this->followLocation = $bool;
	}
	
	public function setLog(ZLog $zLog){
		$this->zLog = $zLog;
	}
	
	public function getContent($url){
		$this->error = "";
		$this->time = 0;
		$this->size = 0;
						
		$startingTime = microtime(true);		
		$curl = curl_init($url);
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($curl,CURLOPT_TIMEOUT,$this->timeout);
		curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, $this->followLocation );		
		
		$result =  curl_exec($curl);
		$this->time = microtime(true) - $startingTime;
		
		if ($result === false){
			$this->error = curl_error($curl);
			$this->logDebug("erreur lors de la récupération de $url : " . $this->error);			
			return false;
		}
		
		$response = curl_getinfo($curl);				
		
		$this->error = $response['http_code'];				
		if ($response['http_code'] != 200){
			$this->logDebug("erreur lors de la récupération de $url : " . $this->error);
			return false;
		}
		
		$this->logDebug("recupération de $url ok");
		$this->size = strlen($result);
		return $result; 				
	}
	
	public function getLastError(){
		return $this->error;
	}
	
	public function getLastTime(){
		return round($this->time * 1000);
	}	
	
	public function getLastSize(){
		return $this->size;
	}
	
	private function logDebug($message){
		if ($this->zLog) {
    		$this->zLog->log(__CLASS__." : $message",ZLog::DEBUG);
		}		
	}
}