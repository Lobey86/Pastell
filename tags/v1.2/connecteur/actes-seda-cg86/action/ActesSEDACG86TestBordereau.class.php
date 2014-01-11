<?php 

class ActesSEDACG86TestBordereau extends ActionExecutor {
	
	public function go(){
		$archivesSEDA = $this->getMyConnecteur();
		
		
		$transactionsInfo = array(
			'numero_acte_collectivite' => mt_rand(),
			'subject' => 'Test de bordereau',
			'decision_date' => date("Y-m-d"),
			'latest_date' => date("Y-m-d"),
			'nature_descr' => 'Arrêtés Individuels',
			'nature_code' => '3',
			'classification' => '3.1',
			'actes_file' => __DIR__.'/../fixtures/delib.pdf',
			'ar_actes' => __DIR__.'/../fixtures/ar-actes.xml',
			'annexe' => array(__DIR__.'/../fixtures/annexe_01.pdf',__DIR__.'/../fixtures/annexe_02.pdf'),
			'echange_prefecture' => 
				array(__DIR__.'/../fixtures/vide.pdf',
				__DIR__.'/../fixtures/vide.pdf',
				__DIR__.'/../fixtures/vide.pdf',
				__DIR__.'/../fixtures/vide.pdf',
				__DIR__.'/../ActesSEDACG86.class.php',
				__DIR__.'/../fixtures/vide.pdf',
				__DIR__.'/../fixtures/vide.pdf',
				__DIR__.'/../fixtures/vide.pdf',
				
				),
			'echange_prefecture_type' => array('2A','2R','3A','3R','3RB','4A','4R','5A'),
			'echange_prefecture_ar' => array('','',__DIR__.'/../fixtures/ar-actes.xml',__DIR__.'/../fixtures/ar-actes.xml','','','','','',''),
			
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