<?php 

class LastMessage {

	const DEFAULT_SESSION_KEY = 'last_message';
	
	private $lastMessage;
	private $lastPost;
	private $sessionKey;
	private $encoding;
	
	public function __construct($sessionKey = null){
		if (empty($sessionKey)){
			 $sessionKey = self::DEFAULT_SESSION_KEY;
		}
		$this->sessionKey = $sessionKey;
		if (isset($_SESSION[$this->sessionKey])){
			$this->lastMessage = $_SESSION[$this->sessionKey];
			unset($_SESSION[$this->sessionKey]);
			$this->lastPost = $_SESSION['last_post'];
			unset($_SESSION['last_post']);
		}
		$this->setEncodingInput(ENT_QUOTES);
	}

	public function getLastMessage(){
		return $this->lastMessage;
	}

	public function getLastPostData(){
		return $this->lastPost;
	}
	
	public function setLastMessage($message){
		$_SESSION[$this->sessionKey] = $message;
		$_SESSION['last_post']=$_POST;
	}
	
	public function setEncodingInput($encoding = ENT_QUOTES){
		$this->encoding = $encoding;
	}
	
	public function getLastInput($inputName){
		if (empty($this->lastPost[$inputName])){
			return false;
		}
		return htmlentities($this->lastPost[$inputName],$this->encoding);
	}
	
}