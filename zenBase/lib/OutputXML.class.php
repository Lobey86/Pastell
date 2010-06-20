<?php 

class OutputXML {
		
	public function getCDATA($data){
		return '<![CDATA[' . str_replace(']]>',']]]]><![CDATA[>',$data) . ']]>';
	}
	
	public function sendTopXML($filename){
		header("content-type: application/xml");
		header("content-disposition: attachement; filename=$filename");
		
		echo "<?xml version='1.0' encoding='UTF-8' ?>\n";
	}
	
}