<?php

class NotificationMail {
	
	private $sqlQuery;
	private $zenMail;
	private $journal;
	private $notification;
	private $notificationDigestSQL;
	
	public function __construct(Notification $notification, ZenMail $zenMail, Journal $journal,NotificationDigestSQL $notificationDigestSQL){
		$this->journal = $journal;
		$this->zenMail = $zenMail;
		$this->notification = $notification;
		$this->notificationDigestSQL = $notificationDigestSQL;
	}
	
	public function notify($id_e,$id_d,$action,$type,$message,array $attachment = array()){
		$lesEmails = $this->notification->getAllInfo($id_e,$type,$action);
		
		foreach($lesEmails as $mail_info){	
			if ($mail_info['daily_digest']) {
				$this->register($mail_info['email'],$id_e,$id_d,$action,$type,$message);	
			}else {	
				$this->sendMail($mail_info['email'],$id_e,$id_d,$action,$type,$message,$attachment);
			}
		}
	}
	
	private function sendMail($mail,$id_e,$id_d,$action,$type,$message,array $attachment = array()){
		$this->zenMail->setEmetteur("Pastell",PLATEFORME_MAIL);
		$this->zenMail->setDestinataire($mail);
		$this->zenMail->setSujet("[Pastell] Notification");
		foreach($attachment as $filename => $filepath){
			$this->zenMail->addAttachment($filename, $filepath);
		}
		$info = array('message'=>$message,'id_e' => $id_e,'id_d'=>$id_d,'action'=>$action,'type'=>$type);
		$this->zenMail->setContenu(PASTELL_PATH . "/mail/notification.php",$info);
		$this->zenMail->send();
		$this->journal->addActionAutomatique(Journal::NOTIFICATION,$id_e,$id_d,$action,"notification envoyée à $mail");
	}
	
	private function register($mail,$id_e,$id_d,$action,$type,$message){
		$this->notificationDigestSQL->add($mail,$id_e,$id_d,$action,$type,$message);
	}
	
	public function sendDailyDigest(){
		$this->zenMail->setEmetteur("Pastell",PLATEFORME_MAIL);
		$this->zenMail->setSujet("[Pastell] Notification (résumé journalier)");
		$all = $this->notificationDigestSQL->getAll();
		foreach($all as $email => $all_info){	
			$this->zenMail->setDestinataire($email);
			$this->zenMail->setContenu(PASTELL_PATH . "/mail/notification-daily-digest.php",$all_info);
			$this->zenMail->send();
			$this->journal->addActionAutomatique(Journal::NOTIFICATION,0,0,false,"Résumé des notifications envoyée à $email");
			foreach($all_info as $info){
				$this->notificationDigestSQL->delete($info['id_nd']);
			}
		}
	}
	
	
}