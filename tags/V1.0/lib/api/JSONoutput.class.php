<?php
class JSONoutput {
	
	public function displayErrorAndExit($Errormessage){
		$result['status'] = 'error';
		$result['error-message'] = $Errormessage;;
		$this->display($result);
		exit;
	}
	
	private function normalize($array){
		if (! is_array($array)){
			return utf8_encode($array);
		}
		$result = array();
		foreach ($array as $cle => $value) {
			$result[utf8_encode($cle)] = $this->normalize($value);
		}
		return $result;
	}
	
	public function display(array $array){	
		//header("Content-type: application/json");
		header("Content-type: text/plain");
		$array = $this->normalize($array);
		echo json_encode($array);
		
	}	
	
}