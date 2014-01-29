<?php 
class APIExecutor {
	
	private $apiAction;
	
	public function __construct(APIAction $apiAction){
		$this->apiAction = $apiAction;
	}
	
	private function getError($Errormessage){
		$result['status'] = 'error';
		$result['error-message'] = $Errormessage;;
		return $result;
	}
	
	public function __call($name,$arguments){
		try {
			$reflexionClass = new ReflectionClass('APIAction');
			$method = $reflexionClass->getMethod($name);
			$result = $method->invokeArgs($this->apiAction,$arguments);
		} catch (Exception $e){
			$result = $this->getError($e->getMessage());
		}
		return utf8_encode_array($result);	
	}
	
	
}