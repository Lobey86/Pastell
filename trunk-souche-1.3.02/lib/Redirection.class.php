<?php

class Redirection{
	
	private $defaultUrl;
	
	public function __construct($url = null){
		$this->defaultUrl = $url;
	}
	
	public function redirect($url = null){
		assert('$url || $this->defaultUrl');
		if ($url == null){
			$url = $this->defaultUrl;
		}
		header("Location: $url");
		exit();
	}
	
}