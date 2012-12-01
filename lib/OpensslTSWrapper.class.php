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
		$dataFilePath = $this->getTmpFile($data);
		$result = $this->execute($this->opensslPath." ts -query -data $dataFilePath");
		unlink($dataFilePath);
		return $result;
	}
	
	public function getTimestampQueryString($timestampQuery){
		$timestampQueryFilePath = $this->getTmpFile($timestampQuery);
		$result =  $this->execute( $this->opensslPath . " ts -query -in $timestampQueryFilePath -text " );
		unlink($timestampQueryFilePath);
		return $result;
	}
	
	public function getTimestampReplyString($timestampReply){
		$timestampReplyFilePath = $this->getTmpFile($timestampReply);
		$commande = $this->opensslPath . " ts -reply -in $timestampReplyFilePath -text " ;
		$result =  $this->execute( $commande );
		unlink($timestampReplyFilePath);
		return $result;
	}
	

	public function verify($data,$timestampReply, $CAFilePath, $certFilePath){
		$dataFilePath = $this->getTmpFile($data);
		$timestampReplyFilePath = $this->getTmpFile($timestampReply);
		
		$command =  $this->opensslPath ." ts -verify " .
					" -data $dataFilePath " . 
					" -in $timestampReplyFilePath " . 
					" -CAfile $CAFilePath" . 
					" -untrusted $certFilePath 2>&1 ";
		$result =  $this->execute( $command);
		unlink($dataFilePath);
		unlink($timestampReplyFilePath);
			
		$this->lastError = $result;
		return false;	
	}
	
}