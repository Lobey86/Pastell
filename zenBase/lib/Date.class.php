<?php 

class Date {
	const DATE_ISO = "Y-m-d H:i:s";
	
	public static function getIsoFromFR($date){
		

		$d = explode('/',$date);
		if (!isset($d[0])){
			$d[0] = date('d');
		}
		if (!isset($d[1])){
			$d[1] = date('m');
		}
		if (!isset($d[2])){
			$d[2] = date('Y');
		}
		$date = sprintf("%04d-%02d-%02d",$d[2],$d[1],$d[0]);
		return $date;
		
	}
	
}
