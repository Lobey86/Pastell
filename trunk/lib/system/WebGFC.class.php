<?php
class WebGFC {
	
	const WSDL = "http://webgfc.test.adullact.org/files/wsdl/webgfc.wsdl";
	
	public function getTypes($collectiviteId){
		$ws = new SoapClient(self::WSDL);
		return $ws->getGFCTypes(array('collectiviteId' => 2));
	}
	
	public function getSousTypes($siren,$type_nom){
		$ws = new SoapClient(self::WSDL);
		//return $ws->getTypes(array('collectiviteId' => $siren));
		return $ws->getSoustypes(2,'Courrier citoyen');
	}

	public function createCourrier($messageSousTypeId,$contact,$titre,$object,$fichier,$username){
		$ws = new SoapClient(self::WSDL);
		return $ws->createCourrier($messageSousTypeId,$contact,$titre,$object,$fichier,$username);
	}
	
	public function setInfo($type_num,$type_message){
		return json_encode(array($type_num,$type_message));
	}
	
	public function getInfo($raw_type_message){
		return json_decode($raw_type_message);
	}
	
}