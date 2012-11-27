<?php
class WebGFC {
	
	const WSDL = "http://webgfc.test.adullact.org/files/wsdl/webgfc.wsdl";
	
	const LOGIN = "pastell";
	const PASSWORD = "pastell";
	
	const USERNAME = "pastell";
	
	private $lastMessage;
	
	private function getSoapClient(){
		return new SoapClient(self::WSDL,array('login' => self::LOGIN, 'password' => self::PASSWORD));
	}
	
	public function getLastMessage(){
		return $this->lastMessage;
	}
	
	public function echoTest($string){
		$ws = $this->getSoapClient();
		return $ws->echotest($string);
	}
	
	public function getTypes($collectiviteId){
		$ws = $this->getSoapClient();
		$data =  $ws->getGFCTypes(1);
		foreach($data as $type){
			$result[$type->anyType[0]] = $type->anyType[1];
		}
		return $result;
	}
	
	public function getSousTypes($siren,$type_nom){
		$ws = $this->getSoapClient();
		$data = $ws->getGFCSoustypes(1,$type_nom);
		foreach($data as $type){
			$result[$type->anyType[0]] = $type->anyType[1];
		}
		return $result;
	}

	public function createCourrier($messageSousTypeId,$contact,$titre,$object,$fichier,$username){
		$ws = $this->getSoapClient();
		$response = $ws->createCourrier($messageSousTypeId,$contact,utf8_encode($titre),utf8_encode($object),$fichier,self::USERNAME);
		
		if ($response->CourrierId != -1){
			$this->lastMessage = $response->CodeRetour . " " . utf8_decode($response->Message);
			return $response->CourrierId;
		}
		$this->lastMessage = $response->CodeRetour . " " .utf8_decode($response->Message); 
		return false;
	}
	
	public function getStatus($courrierID){
		$ws = $this->getSoapClient();
		$response = $ws->getStatut($courrierID);

		$this->lastMessage = $response->CodeRetour . " " .utf8_decode($response->Message);
		
		return $response->CodeRetour;
	}
	
	public function setInfo($type_num,$type_message){
		return json_encode(array($type_num,$type_message));
	}
	
	public function getInfo($raw_type_message){
		return json_decode($raw_type_message);
	}
	
}