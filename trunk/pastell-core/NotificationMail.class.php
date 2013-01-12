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
			$this->zenMail->setEmmeteur("Pastell",PLATEFORME_MAIL);
			$this->zenMail->setDestinataire($mail);
			$this->zenMail->setSujet("[Pastell] Notification");
			$info = array('message'=>$message,'id_e' => $id_e,'id_d'=>$id_d,'action'=>$action,'type'=>$type);
			$this->zenMail->setContenu(PASTELL_PATH . "/mail/notification.php",$info);
			$this->zenMail->send();
			$this->journal->addActionAutomatique(Journal::NOTIFICATION,$id_e,$id_d,$action,"notification envoyée à $mail");
		}
		
	}
	
}