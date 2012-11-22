<?php


class CSVoutput {
	
	private $outstream;
	
	public function sendAttachment($file_name,array $info){
		$this->displayHTTPHeader($file_name);
		$this->display($info);
	}
	
	public function display(array $info){
		$this->begin();
		foreach($info as $line){
			unset($line['preuve']);
			$this->displayLine($line);
		}
		$this->end();
	}
	
	
	public function begin(){
		$this->outstream = fopen("php://output", 'w');
	}
	
	public function displayLine($line){
		fputcsv($this->outstream,$line);
	}
	
	public function end(){
		fclose($this->outstream);
	}
	
	public function displayHTTPHeader($file_name){
		header("Content-type: text/csv; charset=iso-8859-1");
		header("Content-disposition: attachment; filename=$file_name");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
		header("Pragma: public");
	}
	
}