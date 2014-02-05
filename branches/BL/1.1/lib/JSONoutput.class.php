<?php
class JSONoutput {
	
	public function displayErrorAndExit($Errormessage){
		$result['status'] = 'error';
		$result['error-message'] = $Errormessage;;
		$this->display($result);
		exit;
	}
	
	public function display(array $array){	
		$array = utf8_encode_array($array);
		header("Content-type: text/plain");
		echo json_encode($array);	
	}	
	
}