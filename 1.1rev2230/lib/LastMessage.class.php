<?php 

class LastMessage {

	const DEFAULT_SESSION_KEY = 'last_message';
	
	protected $lastMessage;
	protected $lastPost;
	protected $sessionKey;
	protected $encoding;
	
	public function __construct(){
		$this->sessionKey = self::DEFAULT_SESSION_KEY;
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
	
	public function deleteLastInput(){
		unset($_SESSION['last_post']);
	}
	
}