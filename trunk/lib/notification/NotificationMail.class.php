<?php

class NotificationMail {
	
	private $sqlQuery;
	private $zenMail;
	private $journal;
	private $notification;
	
	public function __construct(Notification $notification, ZenMail $zenMail, Journal $journal){
		$this->journal = $journal;
		$this->zenMail = $zenMail;
		$this->notification = $notification;
	}
	
	public function notify($id_e,$id_d,$action,$type,$message){
		
	
		$lesEmails = $this->notification->getMail($id_e,$type,$action);
		
		foreach($lesEmails as $mail){
			
			$this->zenMail->setEmmeteur("Pastell","pastell@sigmalis.com");
			$this->zenMail->setDestinataire($mail);
			$this->zenMail->setSujet("[Pastell] Notification");
			$this->zenMail->setContenu(utf8_encode($message));
			$this->zenMail->send();
			$this->journal->addActionAuto(Journal::NOTIFICATION,$id_e,$id_d,$action,"notification envoyée à $mail");
		}
		
	}
	
}