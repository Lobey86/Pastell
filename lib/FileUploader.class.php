<?php

class FileUploader {
	
	private $files;
	
	public function __construct($files = null){
		if (! $files){
			$files = $_FILES;
		}
		$this->files = $files;
	}
	
	public function getFilePath($filename){
		return $this->getValue($filename,'tmp_name');
	}
	
	public function getName($filename){
		return $this->getValue($filename,'name');
	}
	
	private function getValue($filename,$value){
		if (empty($this->files[$filename][$value])){
			return false;
		}
		return $this->files[$filename][$value];
	}
	
	public function save($filename,$save_path){
		move_uploaded_file($this->getFilePath($filename),$save_path);
	}
	
	public function getAll(){
		$i = 1;
		$result = array();
		foreach($this->files as $filename => $value){
			$result[$filename] = $this->getName($filename);
		}
		return $result;
	}
}	