<?php 

class ValidateBordereau extends ActionExecutor {
	
	public function go(){
		$archivesSEDA = $this->getMyConnecteur();
		
		
		$transactionsInfo = array(
			'unique_id' => mt_rand(),
				'date' => date("Y-m-d"), 
				'description' => 'bla', 
				'pes_retour_description' => 'bla', 
				'pes_aller' => __DIR__."/../fixtures/pes_aller.xml",
				'pes_retour' => __DIR__."/../fixtures/pes_acquit.xml",
				'pes_description' => 'toto',
				'pes_aller_content' => file_get_contents(__DIR__."/../fixtures/pes_aller.xml"),
			
		);
		
		$bordereau = $archivesSEDA->getBordereau($transactionsInfo);	
		
		libxml_use_internal_errors(true);
		$dom = new DOMDocument();
		$dom->loadXML($bordereau);
		$err=  $dom->schemaValidate(__DIR__."/../xsd/seda/archives_echanges_v0-2_archivetransfer.xsd");
		if (!$err){
			$last_error = libxml_get_errors();
			$msg = '';
			foreach($last_error as $err){
				$msg .= "[Erreur #{$err->code}] ".$err->message."\n";
			}
			
			throw new Exception($msg);
		}
		
		$this->setLastMessage("Bordereau valide");
		return true;
	}
	
	
}