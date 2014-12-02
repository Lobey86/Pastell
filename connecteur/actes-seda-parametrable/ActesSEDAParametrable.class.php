<?php

class ActesSEDAParametrableException extends ConnecteurException {}

class ActesSEDAParametrable extends SEDAConnecteur {
	
	private $mimeCode;
	private $xml_file_path;
	private $dico;
	private $condition;
	
	
	public function __construct(MimeCode $mimeCode){
		$this->mimeCode = $mimeCode;
	}
	
	public function  setConnecteurConfig(DonneesFormulaire $seda_config){
		$this->xml_file_path = $seda_config->getFilePath('seda_parametrable');
		$this->dico["#collectiviteNom#"] = $seda_config->get("nom_collectivite");
		$this->dico["#collectiviteSIREN#"] = $seda_config->get("siren");
		$this->condition = array();
                $this->seda_config = $seda_config;
	}
	
	private function traiteNode(DomNode $domNode){
		if ($domNode->nodeType == XML_TEXT_NODE){
			$this->traiteTextNode($domNode);			
			return;
		}
		
		if ($domNode->nodeType == XML_ELEMENT_NODE){
			$this->traiteAttribut($domNode);
			if (! $domNode->parentNode) {
				return;
			}
		}
		$allNode = array();
		//La suppression d'un child lors d'une énumération provoque un undefined behaviour
		foreach($domNode->childNodes as $node){
			$allNode[] = $node;
		}
		foreach($allNode  as $childNode){
			$this->traiteNode($childNode);
		}
	}
	
	
	private function traiteTextNode(DOMText $textNode){
		preg_match_all("/#[\w]+#/", $textNode->nodeValue,$matches);
		foreach($matches as $m){
			foreach($m as $pattern) {
				if (! isset($this->dico[$pattern])){
					throw new ActesSEDAParametrableException("La clé $pattern est inconnue");
					return;
				}
				$textNode->nodeValue = preg_replace("/$pattern/", $this->dico[$pattern], $textNode->nodeValue);
				
			}
		}
	}
	
	private function traiteAttribut(DOMElement $elementNode){
		if ($elementNode->hasAttribute('pastellCondition')){
			$condition = $elementNode->getAttribute('pastellCondition');
			if (! isset($this->condition[$condition])) {
				throw new ActesSEDAParametrableException("La condition $condition n'est pas connu");
			}
			if (! $this->condition[$condition]){
				$parentNode  = $elementNode->parentNode;
				$parentNode->removeChild($elementNode);
				return;
			}
			$elementNode->removeAttribute('pastellCondition');
		}
				
		if($elementNode->hasAttribute('pastellRepetition')){
		
			$repetition = $elementNode->getAttribute('pastellRepetition');
			if ($repetition != 'annexes') {
				throw new ActesSEDAParametrableException("La répetition $repetition n'est pas connu");
			}
			$this->setAnnexe($elementNode);
			return;
		}
				
		foreach($elementNode->attributes as $attr){
			$this->traiteNode($attr);
		}
	}
	
	public function setAnnexe(DomElement $domElement){
		$domElement->removeAttribute('pastellRepetition');
		foreach ($this->annexe as $num_annexe => $annexe){
			$this->dico['#fichierAnnexeNom#'] = basename($annexe);
			$this->dico['#fichierAnnexeMimeCode#'] =  $this->mimeCode->getContentType($annexe);
			$this->dico['#annexeIndex#'] = $num_annexe;
			
			$new_node = $domElement->cloneNode(true);
			$this->traiteNode($new_node);
			$domElement->parentNode->appendChild($new_node);
		}
		$domElement->parentNode->removeChild($domElement);
	}
	

	
	public function getBordereau(array $transactionsInfo){
		
		$transactionInfo2SpecKey = array (
			"numeroInterne" => "numero_acte_collectivite",
			"objet" => "subject",
			"classificationCode" => "classification",
			"dateDecision" => "decision_date",
			"natureCode" => "nature_code",
			"natureLibelle"=>"nature_descr",
		);
		foreach($transactionInfo2SpecKey as $dicokey => $transactionKey) {
			$this->dico["#$dicokey#"] = utf8_encode($transactionsInfo[$transactionKey]);
		}

		$infoArActes = $this->getInfoARActes($transactionsInfo['ar_actes']);
		$this->dico["#identifiantUnique#"] = $infoArActes['IDActe'];		
		$this->dico["#dateAccuseReception#"] = $infoArActes['DateReception'];
		
		$this->dico["#fichierActeNom#"] = basename($transactionsInfo['actes_file']);
		$this->dico["#fichierActeMimeCode#"] = $this->mimeCode->getContentType($transactionsInfo['actes_file']);
		$this->dico['#date#'] = date('c');
        $this->dico['#transferIdentifier#'] = $this->getTransferIdentifier();
        		
		$nb_annexe = count($transactionsInfo['annexe']);
		
		$this->condition = array("annexePresente"=> $nb_annexe,
								"annexeUnique"=>$nb_annexe==1,
								"annexeMultiple"=>$nb_annexe>1);
				
		$this->annexe = $transactionsInfo['annexe'];
		
		$dom = new DOMDocument('1.0');
		$dom->load($this->xml_file_path);
		
		$all_contains = $dom->getElementsByTagName("Contains");
		$containsNode = $all_contains->item(0);
		
		foreach($this->getAllFile($transactionsInfo) as $file_name => $sha1){
			$integrity = $dom->createElement("Integrity");
			$c = $dom->createElement("Contains",$sha1);
			
			$integrity->appendChild($c);
			
			$a = $dom->createAttribute("algorithme");
			$a->value = "http://www.w3.org/2001/04/xmlenc#sha256";
			$c->appendChild($a);
			
			$u = $dom->createElement("UnitIdentifier",$file_name);
			$integrity->appendChild($u);
			$containsNode->parentNode->insertBefore($integrity,$containsNode);
		}
		
		$this->traiteNode($dom);
		return $dom->saveXML();
	}
	
	private function getAllFile($transactionsInfo){
		//TODO ajouté ar_actes
		foreach(array('actes_file') as $key){
			$fileName = $transactionsInfo[$key];
			$result[basename($fileName)] =  hash_file("sha256",$fileName); 
		}
		foreach($transactionsInfo['annexe'] as $fileName){
			$result[basename($fileName)] = hash_file("sha256",$fileName); 
		}
		
		/*foreach($transactionsInfo['echange_prefecture'] as $echange_prefecture){
			$result[basename($echange_prefecture)] = sha1_file($echange_prefecture);
		}
		
		foreach($transactionsInfo['echange_prefecture_ar'] as $echange_prefecture_ar){
			if (! $echange_prefecture_ar){
				continue;
			}
			if (basename($echange_prefecture_ar) == 'empty'){
				continue;
			}
			$result[basename($echange_prefecture_ar)] = sha1_file($echange_prefecture_ar);
		}*/
		return $result;
	}
	
	private function getTransferIdentifier(){
		$numero_transfert = $this->seda_config->get("dernier_numero_transfert");
		if(empty($numero_transfert)) {
			$numero_transfert = 0;
		}
		$numero_transfert++;
		$this->seda_config->setData('dernier_numero_transfert', $numero_transfert);
		return $numero_transfert;
	}
}