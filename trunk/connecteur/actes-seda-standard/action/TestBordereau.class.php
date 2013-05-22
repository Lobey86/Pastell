<?php 

class TestBordereau extends ActionExecutor {
	
	public function go(){
		$archivesSEDA = $this->getMyConnecteur();
		
		
		$transactionsInfo = array(
			'unique_id' => mt_rand(),
			'subject' => 'bla',
			'decision_date' => date("Y-m-d"),
			'latest_date' => date("Y-m-d"),
			'nature_descr' => 'bla',
			'nature_code' => 'bla',
			'classification' => '3.1',
			'actes_file' => '/etc/passwd',
			'ar_actes' => '/etc/passwd',
			'annexe' => array('/etc/passwd'),
		);
		
		$bordereau = $archivesSEDA->getBordereau($transactionsInfo);	
		header("Content-type: text/xml");
		header("Content-disposition: inline; filename=bordereau.xml");
		echo $bordereau;
		exit;
	}
	
}