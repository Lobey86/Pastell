<?php

class ZLog {

	const DEBUG = 1;
	const INFO = 2;
	const NOTICE = 3;
	const WARNING = 4;
	const ERROR = 5;
	const CRITICAL = 6;
	const ALERT = 7;
	const EMERGENCY = 8;

	const DEFAULT_LOG_LEVEL = self::INFO;	

	private $logFile;
	private $startingTime;
	private $logLevel;
	private $tempsGeneration;
	
	public static function getLevelText($logLevel){
		static $logLevelText = array(1 => "debug","info","notice","warning","error","critical","alert","emergency");
		return $logLevelText[$logLevel];
	}
	
	public function __construct($logFile = null){
		if ($logFile == null){
			return;
		}
		$this->setLogLevel(self::DEFAULT_LOG_LEVEL);		
		$this->logFile = $logFile;
		if (! file_exists($logFile)){
			$this->writeToFile("Log file creation\n");
		}
	}	
	
	private function writeToFile($message){
		@ $file = fopen($this->logFile,"a");
		if (!$file){
			throw new Exception("Impossible d'ouvrir le fichier de log ".$this->logFile);
		}
		fwrite($file,$message);
		fclose($file);
	}

	public function setLogLevel($logLevel){
		$this->logLevel = $logLevel;
	}
	
	public function log($message,$logLevel = null){
		if (! $this->logFile){
			return;
		}
		if (! $logLevel){
			$logLevel = self::INFO;
		}
		if ($logLevel < $this->logLevel){
			return;
		}
		$message = date(Date::DATE_ISO).
					" [".self::getLevelText($logLevel)."] " . 
					$message . "\n";		
		$this->writeToFile($message);
	}
}
