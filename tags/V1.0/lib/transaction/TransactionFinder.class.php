<?php 

class TransactionFinder {
	
	private $sqlQuery;
	private $siren;
	private $flux;
	private $allInfo;
	
	private $lastResult;
	
	public function __construct(SQLQuery $sqlQuery){
		$this->sqlQuery = $sqlQuery;
		$this->lastResult = array();
	}
		
	public function setAllInfo(){
		$this->allInfo = true;
	}
	
	public function setSiren($siren){
		$this->siren = $siren;
	}
	
	public function setFlux($flux){
		$this->flux = $flux;
	}
		
	public function getCountResult(){
		if ( ! $this->lastResult){
			$this->getTransaction();
			
		}
		return count($this->lastResult);
		
	}
	
	public function getTransaction(){
		if ($this->lastResult){
			return $this->lastResult;
		}
		if ($this->siren){
			$result =  $this->getTransactionBySiren($this->siren);
		} elseif ($this->flux){
			$result = $this->getTransactionByFlux($this->flux);
		} else {
			$result =  $this->getAllTransaction();
		}
		if ($this->allInfo){
			$result =  $this->completeTransaction($result);
		}
		$this->lastResult = $result;
		return $result;
	}
	
	public function completeTransaction($input){
		if ( ! $input){
			return array();
		}
		$all_id_t = array();
		$result  = array();
		foreach($input as $infoTransaction){
			$result[$infoTransaction['id_t']] = $infoTransaction;
			$all_id_t[] = "'" . $infoTransaction['id_t'] . "'";
			$result[$infoTransaction['id_t']]['role']  = array();
			$result[$infoTransaction['id_t']]['state'] = array();
 		}
		
		$sql = "SELECT * FROM transaction_role " . 
				" JOIN entite ON transaction_role.siren = entite.siren " . 
				" WHERE id_t IN (".implode(',',$all_id_t).")";
		$allRole = $this->sqlQuery->fetchAll($sql);
		foreach($allRole as $role){
			$result[$role['id_t']]['role'][$role['role']] = array( 'siren' => $role['siren'],'denomination' => $role['denomination']) ;
		}
		
		$sql = "SELECT * FROM transaction_changement_etat WHERE id_t IN (".implode(',',$all_id_t).")";
		$allEtat = $this->sqlQuery->fetchAll($sql);
		foreach($allEtat as $etat){
			$result[$etat['id_t']]['state'][$etat['etat']] = $etat['date'] ;
		}
		
		return $result;
	}
	
	public function getAllState(){
		if (! $this->lastResult){
			return array();
		}
		$colonne = array();
		foreach($this->lastResult as $i => $transaction) {
			$colonne =  $colonne + array_keys($transaction['state']);
		}
		return $colonne;
	}
	
	public function getTransactionBySiren($siren){
		if ($this->flux){
			return $this->getTransactionBySirenByFlux($siren,$this->flux);
		}
		return $this->getAllTransactionBySiren($siren);
	}
	
	public function getAllTransaction(){
		$sql = "SELECT * FROM transaction " . 
				" ORDER BY date_changement_etat DESC " ;
		return $this->sqlQuery->fetchAll($sql);		
	}
	
	public function getAllTransactionBySiren($siren){
		$sql = "SELECT * FROM transaction_role "  .  
					" JOIN transaction ON transaction_role.id_t = transaction.id_t " . 
					" AND transaction_role.siren=? " . 
					" ORDER BY date_changement_etat DESC";
		return $this->sqlQuery->fetchAll($sql,array($siren));		
	}
	
	public function getTransactionByFlux($flux){
		$sql = "SELECT * FROM transaction WHERE transaction.type=? " . 
				" ORDER BY date_changement_etat DESC " ;
		return $this->sqlQuery->fetchAll($sql,array($flux));		
	}
	
	public function getTransactionBySirenByFlux($siren,$flux){
		$sql = "SELECT * FROM transaction_role "  .  
					" JOIN transaction ON transaction_role.id_t = transaction.id_t " . 
					" AND transaction.type=? " . 
					" AND transaction_role.siren=? " . 
					" ORDER BY date_changement_etat DESC";
		return $this->sqlQuery->fetchAll($sql,array($flux,$siren));	
	}
	
	public function getTransactionATraiter(){
		 $sql = "SELECT * FROM transaction " . 
				" WHERE  attente_traitement = 1 " ;
		return $this->sqlQuery->fetchAll($sql);		
	}
	
	public function getLastTransactionBySiren($siren, $typeTransaction){
		$sql = "SELECT transaction.id_t FROM transaction " . 
				" JOIN transaction_role ON transaction.id_t = transaction_role.id_t " . 
				" AND siren=? AND type=? " . 
				" ORDER BY date_changement_etat DESC LIMIT 1";
		return $this->sqlQuery->fetchOneValue($sql,array($siren,$typeTransaction));
	}
	
	public function getTransactionByFluxAndState($flux,$state){
		$sql = "SELECT * FROM transaction "  . 
				" WHERE transaction.type=? " . 
				" AND etat = ? " ;
		return $this->sqlQuery->fetchAll($sql,array($flux,$state));	
	}
	
	
}