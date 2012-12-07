<?php
class ActionCreator extends SQL {
	
	private $journal;
	private $id_d;
	
	private $lastAction;
	private $id_a;
	
	public function __construct(SQLQuery $sqlQuery,Journal $journal, $id_d){
		parent::__construct($sqlQuery);
		$this->journal = $journal;
		$this->id_d = $id_d;	
	}
	
	public function addAction($id_e,$id_u,$action,$message_journal){
		
		$now = date(Date::DATE_ISO);
		$this->lastAction = $action;
		
		$sql = "INSERT INTO document_action(id_d,date,action,id_e,id_u) VALUES (?,?,?,?,?)";
		$this->query($sql,$this->id_d,$now,$action,$id_e,$id_u);
				
		$sql = " UPDATE document SET last_action=? WHERE id_d=?";
		$this->query($sql,$action,$this->id_d);
		
		$sql = "UPDATE document_entite SET last_action=? , last_action_date=? WHERE id_d=? AND id_e=?";
		$this->query($sql,$action,$now,$this->id_d,$id_e);
		
		$sql = "SELECT id_a FROM document_action WHERE id_d=? AND date=? AND action=? AND id_e=? AND id_u=?";
		$this->id_a =  $this->queryOne($sql,$this->id_d,$now,$action,$id_e,$id_u);
	
		$this->action = $action;
		$this->date = $now;
		
		$this->addToSQL($id_e,$id_u,$message_journal);
	}
	
	public function addToEntite($id_e,$message_journal){
		$this->addToSQL($id_e,0,$message_journal);		
	}
	
	private function addToSQL($id_e,$id_u,$message_journal){
		assert('$this->id_a');
		
		$sql = "INSERT INTO document_action_entite (id_a,id_e) VALUES (?,?)";
		$this->query($sql,$this->id_a,$id_e);
		
		$this->journal->addSQL(Journal::DOCUMENT_ACTION,$id_e,$id_u,$this->id_d,$this->lastAction,$message_journal);
		
	}
		
}