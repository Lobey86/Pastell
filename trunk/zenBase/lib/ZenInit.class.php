<?php 
require_once("Timer.class.php");
require_once("ZLog.class.php");
require_once("SQLQuery.class.php");
require_once("ZenUser.class.php");

//FIXME cette classe semble faire plusieurs choses ...
class zenInit {

	//@deprecated
	const DEFAULT_DATABASE_NAME = "database.sql";
	
	const DEFAULT_TESTABLE_DIRECTORY = "test";
	
	private $timer;	
	private $writeableDirectory;
	private $testableDirectory;
	private $productName;
	private $defaultDomain;
	private $authorEmail;

	//@deprecated
	private $databaseFileName;
	
	private $userName;
	private $zenUser;
	private $zenUserFile;
	private $sqlQuery;
	private $anonymous;
	
	private $chmodDirectory = 0700;
	private $zLogLevel = ZLog::DEFAULT_LOG_LEVEL;
	
	public function __construct($productName,$writeableDirectory){				
		$this->timer = new Timer();
		$this->productName = $productName;
		$this->writeableDirectory = $writeableDirectory . 
									"/" . $productName;
		if (!file_exists($this->writeableDirectory)){			
			$this->createDirectory($this->writeableDirectory);
		}	
		$this->writeableDirectory = realpath($this->writeableDirectory);
		
		$this->testableDirectory = $this->writeableDirectory."/" . self::DEFAULT_TESTABLE_DIRECTORY; 
		
		$this->setLocalSettings();
		//@deprecated 
		$this->setDatabaseFileName(self::DEFAULT_DATABASE_NAME);		

		//FIXME Ca doit être définie après le setLocalSettings... bon, c'est pas terrible
		if (!file_exists($this->testableDirectory)){			
			$this->createDirectory($this->testableDirectory);
		}	
	}		
	
	public function setUserName($userName){
		$this->userName = $userName;
	}

	//@deprecated 
	public function setDatabaseFileName($fileName){
		$this->databaseFileName = $fileName;
	}
	
	public function setAnonymous(){
		$this->anonymous = true;
	}
	
	public function setLocalSettings(){
		$zenSettingsFile = $this->writeableDirectory . "/LocalSettings.php";		
		@ include_once($zenSettingsFile);
		if (defined('CHMOD_DIRECTORY')){
			$this->chmodDirectory = CHMOD_DIRECTORY;
		}
		if (defined('ZLOG_LEVEL')){
			$this->zLogLevel = ZLOG_LEVEL;
		}
		if (defined('TESTABLE_DIRECTORY')){
			$this->testableDirectory = 	TESTABLE_DIRECTORY;	
		}
		if (defined('DEFAULT_DOMAIN')){
			$this->defaultDomain = DEFAULT_DOMAIN;
		}
		if (defined('AUTHOR_EMAIL')){
			$this->authorEmail = AUTHOR_EMAIL;
		}
	}
	
	public function getAuthorEmail(){
		return $this->authorEmail;
	}
	
	public function getZLog(){
		$logFile = $this->writeableDirectory."/" . $this->productName . ".log";
		$zLog = new ZLog($logFile);		
		$zLog->setLogLevel($this->zLogLevel);
		return $zLog;
	}
	
	private function createDirectory($directory){
		@ $result = mkdir($directory,
							$this->chmodDirectory,
							true);
		if (! $result){
			throw new Exception("Impossible de créer le répertoire ".$directory);
		}
	}
	
	//@deprecated 
	public function getSQLiteDatabase(DatabaseDefinition $database){		
		$sql_file_name = $this->writeableDirectory.'/'.$this->databaseFileName;
		$zLog = $this->getZLog();

		$sqlQuery = new SQLQuery("sqlite:$sql_file_name");
		$sqlQuery->setLog($zLog);

		if (! file_exists($sql_file_name)){
			foreach($database as $tableDefinition){
				$sqlQuery->query($tableDefinition);
			}				
		}	
		return $sqlQuery;	
	}
	
	public function getTime(){
		return round($this->timer->getElapsedTime(),3);
	}
	
	public function getWriteableDirectory() {
		return $this->writeableDirectory;
	}
	
	public function getTestableDirectory(){
		return $this->testableDirectory;
	}

	public function getZenUser(){
		assert('$this->userName');
		if ( ! $this->zenUser){
			$this->zenUser = new ZenUser($this->getZenUserFile(),$this->getSQLQuery());
		}
		if ($this->anonymous){
			$this->zenUser->setAnonymous();
		}
		return $this->zenUser;
	}
	
	public function getZenUserFile(){
		assert('$this->userName');
		if ( ! $this->zenUserFile){
			
			$this->zenUserFile = new ZenUserFile($this->getDatabaseDirectory(),$this->userName);
		}
		return $this->zenUserFile;
	}
	
	private function getDatabaseDirectory(){
		if ($this->anonymous){
				return $this->getTestableDirectory();
		}
		return $this->getWriteableDirectory();
	}
	
	public function getSQLQuery(){
		assert('$this->userName');
		$sqlQuery = new SQLQuery("sqlite:".$this->getZenUserFile()->getFilePath()) ;
		$sqlQuery->setLog($this->getZLog());
		return $sqlQuery;
	}
	
	public function getDefaultDomain(){
		return $this->defaultDomain;
	}
}