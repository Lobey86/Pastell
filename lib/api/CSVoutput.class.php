<?php


class CSVoutput {
	
	public function sendAttachment($file_name,array $info){
		header("Content-type: text/csv; charset=iso-8859-1");
		header("Content-disposition: attachment; filename=$file_name");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
		header("Pragma: public");
		$this->display($info);
	}
	
	public function display(array $info){
		$outstream = fopen("php://output", 'w');

		foreach($info as $line){
				unset($line['preuve']);
			fputcsv($outstream,$line);
		}
		fclose($outstream);
	}
	
}