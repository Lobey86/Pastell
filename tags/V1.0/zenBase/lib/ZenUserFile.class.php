<?php 

class ZenUserFile {
	
	private $filesDirectory;
	private $userName;
	
	public function __construct($filesDirectory,$userName){
		$this->filesDirectory = $filesDirectory;
		$this->userName = $userName;
	}
		
	public function getFilePath(){
		return $this->filesDirectory . "/" . md5($this->userName) . ".sql";	
	}
	
	public function fileExists(){
		return file_exists($this->getFilePath());
	}
		
	public function getName(){
		return $this->userName;		
	}
	
	public function destroy(){
		unlink($this->getFilePath());
	}
}