<?php

class Notification {
	
	private $sqlQuery;
	private $zenMail;
	private $journal;
	
	public function __construct(SQLQuery $sqlQuery,ZenMail $zenMail){
		$this->sqlQuery = $sqlQuery;
		$this->zenMail = $zenMail;
	}
	
	public function setJournal(Journal $journal){
		$this->journal = $journal;
	}
	
	public function notifyAll($id_t,$message){
		$sql = "SELECT mail FROM transaction_role " . 
				" JOIN mail_notification ON transaction_role.siren = mail_notification.siren " .
				" WHERE transaction_role.id_t=?";
		$listMail = $this->sqlQuery->fetchAll($sql,array($id_t));
		foreach($listMail as $mail){
			$this->notify($mail['mail'], $message,$id_t);
		}
	}
	
	public function notify($mail,$message,$id_t){
		$this->zenMail->setEmmeteur("Pastell","pastell@sigmalis.com");
		$this->zenMail->setDestinataire($mail);
		$this->zenMail->setSujet("[Pastell] Notification");
		$this->zenMail->setContenu(utf8_encode($message));
		$this->zenMail->send();
		if ($this->journal) {
			$this->journal->add(Journal::NOTIFICATION,$id_t,"notification envoyée à $mail");
		}
	}
	
}