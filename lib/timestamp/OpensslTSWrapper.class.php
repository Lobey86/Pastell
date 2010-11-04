<?php

class OpensslTSWrapper {
	
	private $opensslPath;
	private $lastError;
	
	public function __construct($opensslPath,ZLog $zLog){
		$this->opensslPath = $opensslPath;
		$this->zLog = $zLog;
	}
	
	public function getLastError(){
		return $this->lastError;
	}
	
	private function execute($command){
		$this->zLog->log($command,ZLog::DEBUG);
		return shell_exec($command);
	}
	
	private function getTmpFile($data = ""){
		$file_path = sys_get_temp_dir()  . "/" . mt_rand();
  		file_put_contents($file_path,$data);
  		return $file_path;
	}
	
	public function getTimestampQuery($data){
		return $this->execute("echo $data | " . $this->opensslPath." ts -query");
	}
	
	public function getTimestampQueryString($timestampQuery){
		$timestampQueryFilePath = $this->getTmpFile($timestampQuery);
		$result =  $this->execute( $this->opensslPath . " ts -query -in $timestampQueryFilePath -text " );
		unlink($timestampQueryFilePath);
		return $result;
	}
	
	public function getTimestampReplyString($timestampReply){
		$timestampReplyFilePath = $this->getTmpFile($timestampReply);
		$result =  $this->execute( $this->opensslPath . " ts -reply -in $timestampReplyFilePath -text " );
		unlink($timestampReplyFilePath);
		return $result;
	}
	
	public function verify(){
		$this->lastError = "Pas encore implémenté";
		return false;	
	}
	
}