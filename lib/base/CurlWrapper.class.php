<?php

class CurlWrapper {
	
	const POST_DATA_SEPARATOR = "\r\n";
	
	private $curlHandle;
	private $lastError;
	private $postData;
	private $postFile;
	
	public function __construct(){
		$this->curlHandle = curl_init();
		$this->setProperties( CURLOPT_RETURNTRANSFER , 1); 
	}

	public function __destruct(){
		curl_close($this->curlHandle);
	}
	
	public function getLastError(){
		return $this->lastError;
	}
	
	private function setProperties($properties,$values){
		curl_setopt($this->curlHandle, $properties, $values); 
	}
	
	public function setServerCertificate($serverCertificate){
		$this->setProperties( CURLOPT_CAINFO ,$serverCertificate ); 
		$this->setProperties( CURLOPT_SSL_VERIFYHOST , 0 ); 
	}
	
	public function setClientCertificate($clientCertificate,$clientKey,$clientKeyPassword)	{
		$this->setProperties( CURLOPT_SSLCERT, $clientCertificate);
		$this->setProperties( CURLOPT_SSLKEY, $clientKey);
		$this->setProperties( CURLOPT_SSLKEYPASSWD,$clientKeyPassword );
	}
	
	public function get($url){
		$this->setProperties(CURLOPT_URL, $url);
		
		if ($this->postData || $this->postFile ){
				$this->curlSetPostData();
		}
		
		$output = curl_exec($this->curlHandle);
		
		$this->lastError = curl_error($this->curlHandle);
		if ($this->lastError){
			$this->lastError = "Erreur de connexion au serveur : " . $this->lastError;
			return false;
		}	
		
		return $output;
	}
	
	public function addPostData($name,$value){
		if ( ! isset($this->postData[$name])){
			$this->postData[$name] = array();
		}
		
		$this->postData[$name][] = $value;
	}
	
	public function addPostFile($field,$filePath,$fileName = false){
		if (! $fileName){
			$fileName = basename($filePath);
		}
		$this->postFile[$field][$fileName] = $filePath;
	}
	
	private function getBoundary(){
		return '----------------------------' .
	        substr(sha1( 'CurlWrapper' . microtime()), 0, 12);
	}
	
	private function curlSetPostData( ) {
	   	//cURL ne permet pas de poster plusieurs fichiers avec le même nom ! 
		//cette fonction est inspiré de http://blog.srcmvn.com/multiple-values-for-the-same-key-and-file-upl
		$this->setProperties(CURLOPT_POST,true);
		
		
	    $boundary = $this->getBoundary();
	
	    $body = array();
	    
	    foreach ( $this->postData as $name => $multipleValue ) {
	    	foreach($multipleValue as $value ){
	    		$body[] = "--$boundary";
	            $body[] = "Content-Disposition: form-data; name=$name";
	            $body[] = '';
	            $body[] = $value;
	    	}
	    }
	  	foreach ( $this->postFile as $name => $multipleValue ) {
	    	foreach($multipleValue as $fileName => $filePath ){
	    		$body[] = "--$boundary";
				$body[] = "Content-Disposition: form-data; name=$name; filename=$fileName";
	            $body[] = 'Content-Type: application/octet-stream';
	            $body[] = '';
	            $body[] = file_get_contents($filePath);
	    	}
	    }	

	    $body[] = "--$boundary--";
	    $body[] = '';
	    
	    $content = join(self::POST_DATA_SEPARATOR, $body);
	    
	    $curlHttpHeader[] = 'Content-Length: ' . strlen($content);
		$curlHttpHeader[] = 'Expect: 100-continue';
		$curlHttpHeader[] = "Content-Type: multipart/form-data; boundary=$boundary";	
	
	    $this->setProperties( CURLOPT_HTTPHEADER, $curlHttpHeader);
	    $this->setProperties( CURLOPT_POSTFIELDS, $content);
	}
	
	
}