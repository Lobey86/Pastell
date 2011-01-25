<?php

class CSV {
	
	
	public function get($file_path){
		$result = array();

		$file = fopen($file_path, "r");
		if ( ! $file ){
			return array();
		}
		
		while (($data = fgetcsv($file, 1000, ";")) !== FALSE) {
    		$result[] = $data ;
    	}
    	fclose($file);
		return $result;
	}
	
}