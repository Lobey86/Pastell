<?php
class SoapClientFactory {
	
	private $lastError;
	
	public function getLastError(){
		return $this->lastError;
	}
	
	public function getInstance($wsdl,array $options = array()){
		try {
			$soapClient = new NotBuggySoapClient($wsdl, $options);
		} catch(Exception $e){
			$this->lastError = $e->getMessage();
			return false;
		}
		return $soapClient;
	}
	
}

class NotBuggySoapClient extends SoapClient {
	
	private $is_jax_ws;
	
	//PHP SUCKS : https://bugs.php.net/bug.php?id=47584	
	public function __construct($wsdl,array $options = array(),$is_jax_ws = false){
		$this->is_jax_ws = $is_jax_ws;
		$options['exceptions'] = 1;
		if (function_exists('xdebug_disable')) {
  			xdebug_disable();
		}
		parent::__construct($wsdl,$options); 
  		if (function_exists('xdebug_enable')) {
  			xdebug_enable();
		}
	}
	
	//http://stackoverflow.com/questions/5948402/having-issues-with-mime-headers-when-consuming-jax-ws-using-php-soap-client	
	public function __doRequest($request, $location, $action, $version, $one_way = 0) {	
    	$response = parent::__doRequest($request, $location, $action, $version, $one_way);
    	if ($this->is_jax_ws){
			$response = strstr($response,"<?xml");
	        $response = strstr($response,"--uuid:",true);
    	}
		return $response;
    }
    
}