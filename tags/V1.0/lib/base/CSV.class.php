<?php

class CSV {
	
	private function openFile($file_path){
		
		$fileInfo = new finfo();
		
		$info = $fileInfo->file($file_path,FILEINFO_MIME_TYPE);
		
		if ($info == 'application/x-gzip'){
			return gzopen($file_path,"r");			
		}
		return fopen($file_path, "r");

	}
	
	public function get($file_path){
		$result = array();

		$file = $this->openFile($file_path);
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