<?php 

class TestBordereau extends ActionExecutor {
	
	public function go(){
		$heliosArchivesSEDA = $this->getMyConnecteur();
		
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
		
		
		
		$bordereau = $heliosArchivesSEDA->getBordereau($transactionsInfo);	
		header("Content-type: text/xml");
		header("Content-disposition: inline; filename=bordereau.xml");
		echo $bordereau;
		exit;
	}
	
}