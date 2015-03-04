<?php
class HeliosSignature {
	
	public function getInfoForSignature($xml_file_path){
		$xml = simplexml_load_file($xml_file_path);

		if ($xml->PES_DepenseAller){
			$root = $xml->PES_DepenseAller;
		} else if($xml->PES_RecetteAller) {
			$root = $xml->PES_RecetteAller;
		} else {
			throw new Exception("Le bordereau ne contient ni Depense ni Recette");			
		}
		
		$id = array();
		$hash = array();
		foreach($root->Bordereau as $bordereau){
			$dom = dom_import_simplexml($bordereau);
			
			//Si la balise Bordereau n'a pas d'attribut Id (qui est facultatif), on met l'id qu'on trouve à l'interieur du BlocBordereau
			if (! $dom->hasAttribute('Id')){
				if (empty($bordereau->BlocBordereau->IdBord['V'])){
					throw new Exception("Au moins un bordereau du fichier PES ne contient pas d'identifiant valide : signature impossible");
				}
				$dom->setAttribute('Id', strval($bordereau->BlocBordereau->IdBord['V']));
			}
			$id[]=$dom->getAttribute('Id');
			$data_to_sign = $dom->C14N(true, false);
			$hash[] = sha1($data_to_sign);
		}
		
		$info = array();
		$info['bordereau_hash'] = implode(",",$hash);
		$info['bordereau_id'] = implode(",",$id);
		
		return $info;
	}

	public function injectSignature($original_file_path,$signature){
		
		$all_signature = explode(",",$signature);
		
		$domDocument = new DOMDocument();
		$domDocument->load($original_file_path);
		
		$all_bordereau = $domDocument->getElementsByTagName('Bordereau');
		
		foreach($all_signature as $num_bordereau => $signature) {
			$signature_1 = base64_decode($signature);
			$signatureDOM = new DOMDocument();
			$signatureDOM->loadXML($signature_1);
			$signature = $signatureDOM->firstChild->firstChild;
			$cloned = $signature->cloneNode(TRUE);
			
			$bordereauNode = $all_bordereau->item($num_bordereau);
			if (! $bordereauNode->hasAttribute('Id')){
				$bordereauSimpleXML = simplexml_import_dom($bordereauNode);
				$bordereauNode->setAttribute('Id', strval($bordereauSimpleXML->BlocBordereau->IdBord['V']));
			}
			
			$bordereauNode->appendChild($domDocument->importNode($cloned,true));
		}
		//$domDocument->formatOutput = TRUE;
		return $domDocument->saveXml();
		
		
	}
	
}