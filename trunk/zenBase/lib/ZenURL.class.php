<?php 

class ZenURL {
	
	private $_server;
	
	public function __construct(array $_server){
		$this->_server = $_server;	
	}
	
	public function getWebDirectoryOfScript(){
		assert('$this->_server["SERVER_NAME"]');
		assert('$this->_server["PHP_SELF"]');
		return 'http://' . $this->_server['SERVER_NAME'] . dirname($this->_server['PHP_SELF']) ;
	}
	
	
}
