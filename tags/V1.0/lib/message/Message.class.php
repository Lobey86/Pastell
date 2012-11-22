<?php


class Message2 {
	
	private $messageType;
	
	public function __construct($message_type){
		$this->messageType = $message_type;
	}
	
	public function getFormulaire(){
		return "rh-arrete.yml";
	}
	
	
	
}