<?php 

class TestBordereau extends ActionExecutor {
	
	public function go(){
		$heliosArchivesSEDA = $this->getMyConnecteur();
		
		$transactionsInfo = array(
				'unique_id' => mt_rand(),
				'date' => date("Y-m-d"), 
				'description' => 'bla', 
				'pes_retour_description' => 'bla', 
				'pes_aller' => '/etc/passwd',
				'pes_retour' => '/etc/group',
				'pes_description' => 'toto',
		);
		
		$bordereau = $heliosArchivesSEDA->getBordereau($transactionsInfo);	
		header("Content-type: text/xml");
		header("Content-disposition: inline; filename=bordereau.xml");
		echo $bordereau;
		exit;
	}
	
}