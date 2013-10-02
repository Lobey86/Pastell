<?php
class VerifEnvironnement {
	
	private $last_error;
	
	public function getLastError(){
		return $this->last_error;
	}
	
	public function checkPHP(){
		return array("min_value" => "5.3","environnement_value" => phpversion()); 
	}
	
	public function checkExtension(){ 
		$extensionNeeded = array("curl","mysql","openssl","simplexml","imap","apc","soap","bcmath","ssh2","pdo","pdo_mysql","zip","phar");
		foreach($extensionNeeded as $extension){
			$result[$extension] = extension_loaded($extension);
		}
		return $result;
	}
	
	public function checkModule(){
		$moduleNeeded = array("Mail.php","Mail/mime.php","CAS.php");
		foreach($moduleNeeded as $module){
			$result[$module] = @ include_once($module);
		}
		return $result;
	}
	
	public function checkWorkspace(){
		if (! defined("WORKSPACE_PATH")){
			$this->last_error = "WORKSPACE_PATH n'est pas défini"; 
			return false;
		}
		if (! is_readable(WORKSPACE_PATH)) {
			$this->last_error = WORKSPACE_PATH ." n'est pas accessible en lecture"; 
			return false;
		}
		if (! is_writable(WORKSPACE_PATH)) {
			$this->last_error = WORKSPACE_PATH ." n'est pas accessible en écriture"; 
			return false;
		}
		return true;
	}
	
}