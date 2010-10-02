<?php


//PAS FINI

class NavigationEntite {
	
	private $sqlQuery;
	private $id_u;
	private $id_e;
	
	public function __construct($sqlQuery,$id_u,$id_e){
		$this->sqlQuery = $sqlQuery;
		$this->id_u = $id_u;
	}
	
	
	public function getEntiteList(){
		
		
		return array("titit");
	}
	
	
	
	
	
}