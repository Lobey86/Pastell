<?php


class ActionSendCDG {
	
	private $sqlQuery;
	
	public function __construct(SQLQuery $sqlQuery){
		$this->sqlQuery = $sqlQuery;
	}
	
	public function go($id_d){
		//Récuperer le siren de la collectivité
		//Récuperer son centre de gestion
		
		//Vérifier que le document est envoyable
		
		//Rendre le document inéditable
		//Mettre à jour les droits

		//Notification + journal des transactions
		
	}
	
	
	
}