<?php

class TimestampReplyCreator {
	
	private $opensslPath;
	private $signerCertificate;
	private $signerKey;
	private $signerKeyPassword;
	private $configFile;
		
	public function __construct($opensslPath,$signerCertificate,$signerKey,$signerKeyPassword,$configFile){
		$this->opensslPath = $opensslPath;
		$this->signerCertificate = $signerCertificate;
		$this->signerKey = $signerKey;
		$this->signerKeyPassword = 	$signerKeyPassword;
		$this->configFile = $configFile;
	}
	
	private function getTmpFile($data = ""){
		$file_path = sys_get_temp_dir()  . "/" . mt_rand();
  		file_put_contents($file_path,$data);
  		return $file_path;
	}
	
	public function createTimestampReply($timestampRequest){		
		$timestampRequestFile = $this->getTmpFile($timestampRequest);
		$timestampReplyFile = $this->getTmpFile("");
		
		$command = $this->opensslPath . " ts -reply " . 
					" -queryfile $timestampRequestFile" .
					" -signer " . $this->signerCertificate . 
					" -inkey " . $this->signerKey . 
					" -passin pass:".$this->signerKeyPassword . 
					" -out $timestampReplyFile " .
					" -config " . $this->configFile;
		shell_exec($command);
		
		$timestampReply = file_get_contents($timestampReplyFile);
		unlink($timestampRequestFile);
		unlink($timestampReplyFile);
		return $timestampReply;
	}	
}