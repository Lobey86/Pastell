<?php 

class LastError extends LastMessage {

	const DEFAULT_SESSION_KEY = 'last_error';
	private $lastError;
	
	public function __construct(){
		$this->sessionKey = self::DEFAULT_SESSION_KEY;
		if (isset($_SESSION[$this->sessionKey])){
			$this->lastMessage = $_SESSION[$this->sessionKey];
			unset($_SESSION[$this->sessionKey]);
			if (isset($_SESSION['last_post'])) {
				$this->lastPost = $_SESSION['last_post'];
				unset($_SESSION['last_post']);
			} else {
				$this->lastPost = false;
			}
		}
		$this->setEncodingInput(ENT_QUOTES);
	}

	public function getLastError(){
		return parent::getLastMessage();
	}
	
	public function setLastError($message){
		parent::setLastMessage($message);
	}

}