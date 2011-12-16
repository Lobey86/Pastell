<?php
   
function is_mail($mail){
	if (preg_match('/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i',$mail)){
		return true;
	}
	
	if (preg_match('/^[^@<]*<([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})>$/i',$mail)){
		return true;
	}
	
	if (preg_match('/^groupe: ".*"$/',$mail)){
		return true;
	}
	
	if (preg_match('/^role: ".*"$/',$mail)){
		return true;
	}

	return false;
}

function get_mail_list($scalar_mail_list){
	$mails = array(0=>'');
	$i = 0;
	$state = 1;
	foreach(str_split($scalar_mail_list) as $letter){
		if ($letter == '"'){
			$state = 1 - $state;
		}
		if ($letter == ',' && $state){
			$mails[++$i] = '';
		} else {
			$mails[$i].=$letter;
		}
	}
	$result = array();
	foreach($mails as $mail){
		$mail = trim($mail);
		if ($mail) {
			$result[] = $mail;
		} 
		
	}
	return array_unique($result);
}

function is_mail_list($scalar_mail_list){
	
	foreach(get_mail_list($scalar_mail_list) as $mail){
		if (! $mail){
			continue;
		}
		if ( ! is_mail(trim($mail))){
			return false;
		}
	}
	return true;
}