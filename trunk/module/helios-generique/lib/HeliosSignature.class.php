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

		// Signature par bordereau
		$id = array();
		$hash = array();        
        // Est-ce que chacun des bordereaux possède un Id ?
        $isIdInBordereau = true;
        foreach($root->Bordereau as $bordereau){
            $dom = dom_import_simplexml($bordereau);
            if($dom->hasAttribute('Id')) {
                $isIdInBordereau = $isIdInBordereau && true;
            }
            else {
                $isIdInBordereau = false;
            }
        }
        
        // Si oui, on signe chacun des bordereaux
        if( $isIdInBordereau ){
            foreach($root->Bordereau as $bordereau){
                $dom = dom_import_simplexml($bordereau);
                $isBordereau = true;
				$id[]=$dom->getAttribute('Id');
				$data_to_sign = $dom->C14N(true, false);
				$hash[] = sha1($data_to_sign);
            }
        }
        // sinon
        else {
                //On vérifie que la balise PES_çAller possède un ID
            if( isset( $xml['Id'] ) && !empty($xml['Id'] ) ) {
                $domGlobal = dom_import_simplexml($xml);
                $isBordereau = false;
                $id[] = $domGlobal->getAttribute('Id');
                $data_to_sign = $domGlobal->C14N(true, false);
                $hash[] = sha1($data_to_sign);
            }
            else {
                throw new Exception("Le bordereau du fichier PES ne contient pas d'identifiant valide, ni la balise PESAller : signature impossible");
            }
        }
        

		$info = array();
		if($isBordereau) {
			$info['isbordereau'] = true;
			$info['bordereau_hash'] = implode(",",$hash);
			$info['bordereau_id'] = implode(",",$id);
		}
		else {
			$info['isbordereau'] = false;
			$info['flux_hash'] = implode(",",$hash);
			$info['flux_id'] = implode(",",$id);
		}

		return $info;
	}

	public function injectSignature($original_file_path,$signature, $isBordereau){
		
		$all_signature = explode(",",$signature);

		$domDocument = new DOMDocument();
		$domDocument->load($original_file_path);
	
		if( $isBordereau ) {
			$all_bordereau = $domDocument->getElementsByTagName('Bordereau');

			foreach($all_signature as $num_bordereau => $signature) {
				$signature_1 = base64_decode($signature);
				$signatureDOM = new DOMDocument();
				$signatureDOM->loadXML($signature_1);
				$signature = $signatureDOM->firstChild->firstChild;
				$cloned = $signature->cloneNode(TRUE);
				
				$bordereauNode = $all_bordereau->item($num_bordereau);

				$bordereauNode->appendChild($domDocument->importNode($cloned,true));
			}
		}
		else {
			$signature_1 = base64_decode($signature);
			$signatureDOM = new DOMDocument();
			$signatureDOM->loadXML($signature_1);
            $signature = $signatureDOM->firstChild->firstChild;
			
            $rootNode = $domDocument->documentElement;
            $rootNode->appendChild($domDocument->importNode($signature,true));
		}

		//$domDocument->formatOutput = TRUE;
		return $domDocument->saveXml();
		
		
	}
	
}