<?php
require_once( PASTELL_PATH . "/lib/timestamp/OpensslTSWrapper.class.php");
require_once( PASTELL_PATH . "/lib/base/CurlWrapper.class.php");

class SignServer {
	
	private $url;
	private $curlWrapper;
	private $lastError;
	private $opensslTSWrapper;
	private $lastTimestamp;
	
	public function __construct($url,OpensslTSWrapper $opensslTSWrapper){
		$this->url = $url;
		$this->setCurlWrapper(new CurlWrapper());
		$this->opensslTSWrapper = $opensslTSWrapper;
	}
	
	public function setCurlWrapper(CurlWrapper $curlWrapper){
		$this->curlWrapper = $curlWrapper;
	}
	
	public function getLastError(){
		return $this->lastError;
	}
	
	public function getTimestampReply($data){
		$this->lastTimestamp = "";
		$timestampRequest = $this->opensslTSWrapper->getTimestampQuery($data);
		
		$url = $this->url . "&encoding=base64&data=" . urlencode(base64_encode($timestampRequest));
		$timestampReply = $this->curlWrapper->get($url);
	  	if (! $timestampReply){
	  		$this->lastError = $this->curlWrapper->getLastError();
	  		return false;
	  	}
	  	
	  	$reply = $this->opensslTSWrapper->getTimestampReplyString($timestampReply);
	  	
	  	
	  	preg_match("/Time stamp: (.*)\n/",$reply,$matches);
	  	
	  	if (! isset($matches[1])){
	  		$this->lastTimestamp = false;
	  		$this->lastError = "Le timestamp n'a pas pu être récupéré";
	  		return false;
	  	}
	  	
	  	$this->lastTimestamp = date(Date::DATE_ISO,strtotime($matches[1]));
	  	
	  	return $timestampReply;
	}
	
	public function getLastTimestamp(){
		return $this->lastTimestamp;
	}
	
}