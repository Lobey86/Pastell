<?php

abstract class SEDAConnecteur extends  Connecteur {
	
	abstract public function getBordereau(array $transactionsInfo);
	
	protected function getInfoARActes($file_path){
		$file_name = basename($file_path);
		@ $xml = simplexml_load_file($file_path);
		if ($xml === false){
			throw new Exception("Le fichier AR actes $file_name n'est pas exploitable");
		}
		$namespaces = $xml->getNameSpaces(true);
		if (empty($namespaces['actes'])){
			throw new Exception("Le fichier AR actes $file_name n'est pas exploitable");
		}
		
		$attr = $xml->attributes($namespaces['actes']);
		if (!$attr){
			throw new Exception("Le fichier AR actes $file_name n'est pas exploitable");
		}
		return array('DateReception' => $attr['DateReception'],'IDActe'=>$attr['IDActe']);
	}
	
	public function getIntegrityMarkup($fileName){
		$node = new ZenXML("Integrity");
		$node->Contains = hash_file("sha256",$fileName); 
		$node->Contains['algorithme'] = "http://www.w3.org/2001/04/xmlenc#sha256";
		$node->UnitIdentifier = basename($fileName);
		return $node;
	}
	
}