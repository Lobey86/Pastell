<?php

class ActionSendTdT {
	
	private $sqlQuery;
	
	public function __construct(SQLQuery $sqlQuery){
		$this->sqlQuery = $sqlQuery;
	}
	
	public function go($id_d){
		
		//Vérifier que le document est envoyable
		
		//Rendre le document inéditable
		//Utiliser l'API TdT et envoyer le document
		//Notification + journal des transactions
		
	}
	
	
	
}