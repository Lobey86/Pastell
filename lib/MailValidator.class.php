<?php 
require_once("Mail/RFC822.php");
require_once("PEAR.php");

class MailValidator {
		
	public function isValid($email){
		$lo_mail = Mail_RFC822::parseAddressList($email, NULL, FALSE);
		if(PEAR::isError($lo_mail)){    
    		return false;
		} elseif ($lo_mail[0]->host=='localhost'){
			return false;
		}
		return true;
	}
	
}