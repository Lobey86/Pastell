<?php

class MessageRessource {
	
	private $sqlQuery;
	private $id_r;
	
	public function __construct(SQLQuery $sqlQuery,$id_r){
		$this->sqlQuery = $sqlQuery;
		$this->id_r = $id_r;
	}
	
	public function getInfo(){
		$sql = "SELECT message.*,message_ressource.* FROM message_ressource JOIN message ON message_ressource.id_m = message.id_m WHERE id_r=?";
		return $this->sqlQuery->fetchOneLine($sql,array($this->id_r));
	}
	
}