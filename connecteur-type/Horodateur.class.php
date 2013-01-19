<?php
abstract class Horodateur extends Connecteur {
	
	protected $opensslTSWrapper;
		
	public function __construct(OpensslTSWrapper $opensslTSWrapper){
		$this->opensslTSWrapper = $opensslTSWrapper;
	}	
	
	public function getTimeStamp($token_reply){
		$reply = $this->opensslTSWrapper->getTimestampReplyString($token_reply);
	  	preg_match("/Time stamp: (.*)\n/",$reply,$matches);	  	
	  	if (isset($matches[1])){
	  		return date(Date::DATE_ISO,strtotime($matches[1]));
	  	}
	}
	
	abstract public function getTimestampReply($string_to_sign);
	
	abstract public function verify($data,$token);	
}