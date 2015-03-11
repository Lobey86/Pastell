<?php 

class API_JSON extends APIExecutor {
	
	private $jsonOutput;
	
	public function __construct(APIAction $apiAction,JSONoutput $jsonOutput){
		parent::__construct($apiAction);
		$this->jsonOutput = $jsonOutput;
	}
	
	public function __call($name,$arguments){
		$result = parent::__call($name,$arguments);
		header("Content-type: text/plain");
		echo json_encode($result);	
	}
	
}