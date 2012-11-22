<?php 

class LastError extends LastMessage {

	const DEFAULT_SESSION_KEY = 'last_error';
	private $lastError;
	
	public function __construct(){
		parent::__construct(self::DEFAULT_SESSION_KEY);
	}

	public function getLastError(){
		return parent::getLastMessage();
	}
	
	public function setLastError($message){
		parent::setLastMessage($message);
	}

}