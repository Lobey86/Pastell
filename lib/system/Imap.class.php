<?php 

class Imap {
	
	const DEFAULT_PORT = "993";
	const DEFAULT_MAILBOX_STRING = "INBOX";
	const DEFAULT_OPTION="imap/ssl/novalidate-cert";

	private $mailBox;
	private $mailCheck;
	
	private $login;
	private $password;
	private $server;
	private $port;
	private $mailBoxString;
	private $option;
	
	public function __construct($server,$login,$password){
		$this->login = $login;
		$this->password = $password;
		$this->setServer($server);		
		$this->setPort(self::DEFAULT_PORT);
		$this->setMailBox(self::DEFAULT_MAILBOX_STRING);
		$this->setOption(self::DEFAULT_OPTION);
	}
	
	public function setServer($server){
		$this->server = $server;
	}
	
	public function setPort($port){
		$this->port = $port;
	}
		
	public function setMailBox($mailBox){
		$this->mailBoxString = $mailBox;
	}
	
	public function setOption($option){
		$this->option = $option; 
	}	
		
	public function open(){
		@ $this->mailBox = imap_open($this->getMailBoxString(),$this->login, $this->password);
		if ($this->mailBox == false) {
			throw new Exception("Impossible d'ouvrir la boite aux lettres (".$this->mailBoxString .") : " . imap_last_error());
		}
		$this->mailCheck = imap_check($this->mailBox);
	}
	
	private function getServerString(){
		$mailBoxString = "{".$this->server.":".$this->port;
		if ($this->option){
			$mailBoxString .= "/".$this->option;
		}
		$mailBoxString .="}";
		return $mailBoxString;	
	}
	
	private function getMailBoxString(){
		return $this->getServerString().$this->mailBoxString;
		
	}
	
	public function getNbMessage(){
		return $this->mailCheck->Nmsgs;
	}

	public function fetchOverview($min,$max) {
		return imap_fetch_overview($this->mailBox,"$min:$max");
	}	
	
	public function fetchOverviewByUid($uid){
		$overview = imap_fetch_overview($this->mailBox,$uid,FT_UID);
		if ( ! $overview) {
			throw new Exception("Message $uid does not exist ...");
		}

		return $overview;
	}
	
	public function fetchOverviewById($id){
		$overview = imap_fetch_overview($this->mailBox,$id);
		if ( ! $overview) {
			throw new Exception("Message $uid does not exist ...");
		}
		return $overview;
 
	}
	
	public function getFetchHeader($uid){
		return imap_fetchheader($this->mailBox,$uid,FT_UID);
	}
	
	public function getBody($uid){
		 return imap_body($this->mailBox,$uid,FT_UID);
	}
		
	public function getMessageStructure($uid){
		$structure = imap_fetchstructure($this->mailBox,$uid,FT_UID);
		//print_r($structure);
		//print_r(serialize($structure));
		return $structure;
	}
	
	public function getBodyPart($uid,$part){
		return imap_fetchbody($this->mailBox,$uid,$part,FT_UID);
	}
	public function getMessageUid($id){
		@ $uid = imap_uid($this->mailBox,$id);
		if (! $uid) {
			throw new Exception("Message $id does not exist ...");
		}
		return $uid;
	}
	
	public function getMessageId($uid){
		@ $id = imap_msgno($this->mailBox,$uid);
		if (! $id) {
			throw new Exception("Message $uid does not exist ...");
		}
		return $id;
	}
	
	public function listMailBox(){
		return imap_list($this->mailBox,"{" . $this->server . "}","*");
	}

	public function listMailBoxWithDetail(){
		return imap_getmailboxes($this->mailBox,"{" . $this->server . "}","*");
	}

	public function search($criteria){
		return imap_search($this->mailBox,$criteria,SE_UID);
	}
	
	public function copySent($data){
		imap_append($this->mailBox,$this->getServerString()."Sent", $data);
	}
	
	public function markUndeleted($uid){
		imap_undelete($this->mailBox , $uid  ,SE_UID );
	}
	
	public function markDeleted($uid){
		imap_delete($this->mailBox , $uid  ,SE_UID );
	}
	
	public function expunge(){
		imap_expunge($this->mailBox);
	}
	
	public function mailboxStatus(){
		return imap_status($this->mailBox,$this->getMailBoxString(),SA_ALL);
	}
	
}