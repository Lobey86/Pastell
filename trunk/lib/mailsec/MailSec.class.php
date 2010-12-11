<?php


class MailSec {
	
	public function __construct($key){
		$this->key = $key;
	}
	
	public function getInfo(Journal $journal){
		
		$documentEmail = new DocumentEmail($sqlQuery);
		
		$opensslTSWrapper = new OpensslTSWrapper(OPENSSL_PATH,$zLog);
		$signServer = new SignServer(SIGN_SERVER_URL,$opensslTSWrapper);
		$journal = new Journal($signServer,$sqlQuery,0);
		
		$info  = $documentEmail->consulter($key,$journal);
		
	}
	
}