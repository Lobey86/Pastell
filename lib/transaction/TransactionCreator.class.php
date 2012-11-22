<?php

class TransactionCreator {
	
	const MAX_ESSAI = 5;
	
	private $sqlQuery;
	private $passwordGenerator;
	
	public function __construct(SQLQuery $sqlQuery, PasswordGenerator $passwordGenerator){
		$this->sqlQuery = $sqlQuery;
		$this->passwordGenerator= $passwordGenerator;
	}
	
	public  function getNewTransactionNum(){
		for ($i=0; $i<self::MAX_ESSAI; $i++){
			$id_t = $this->passwordGenerator->getPassword();
			$sql = "SELECT count(*) FROM transaction WHERE id_t=?";
			$nb = $this->sqlQuery->fetchOneValue($sql,array($id_t));
			
			if ($nb == 0){
				return $id_t;
			}	
		}
		throw new Exception("Impossible de trouver un numéro de transaction");
	}
}