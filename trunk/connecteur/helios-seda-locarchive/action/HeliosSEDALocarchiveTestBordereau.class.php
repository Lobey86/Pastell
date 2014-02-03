<?php 

class HeliosSEDALocarchiveTestBordereau extends ActionExecutor {
	
	public function go(){
		$archivesSEDA = $this->getMyConnecteur();
		
		
		$transactionsInfo = array(
			'unique_id' => '42',
			'date' => date('Y-m-d'),
			'description'=> 'PES test bordereau',
			'pes_description'=>'PES description',
			'pes_retour_description'=>'PES retour description',
			'pes_aller'=>'pes_aller.xml',
			'pes_retour'=>'pes_retour.xml'
			
		);
		
		$bordereau = $archivesSEDA->getBordereau($transactionsInfo);	
		if($this->from_api){
			$this->setLastMessage($bordereau);
			return true;
		}
		header("Content-type: text/xml");
		header("Content-disposition: inline; filename=bordereau.xml");
		echo $bordereau;
		exit;
	}
	
	public function validateBordereau($bordereau){		
		libxml_use_internal_errors(true);
		$dom = new DOMDocument();
		$dom->loadXML($bordereau);
		
		$err=  $dom->schemaValidate(__DIR__."/../xsd/seda/archives_echanges_v0-2_archivetransfer.xsd");
		$this->lastError = libxml_get_errors();
		return $err;
	}
	
	public function getLastError(){
		$msg = "<ul>";
		foreach($this->lastError as $err){
			$msg .= "<li>[Erreur #{$err->code}] ".$err->message."</li>";
		}
		return $msg."</ul>";
	}
	
	
	
	
}