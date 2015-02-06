<?php

function get_hecho($message,$quote_style=ENT_QUOTES){
	return htmlentities($message,$quote_style,"iso-8859-15");
}

function hecho($message,$quot_style=ENT_QUOTES){
	echo get_hecho($message,$quot_style); 
}

function getDateIso($value){
	if ( ! $value){
		return "";
	}
	return preg_replace("#^(\d{2})/(\d{2})/(\d{4})$#",'$3-$2-$1',$value);
}


function rrmdir($dir) {	
	if (! is_dir($dir)) {
		return;
	}
	foreach ( scandir($dir) as $object) {
		if (in_array($object,array(".",".."))) {
			continue;
		}
		if (is_dir("$dir/$object")){
			rrmdir("$dir/$object");
		} else {
			unlink("$dir/$object");
		}
	}
	rmdir($dir);
}


function get_argv($num_arg) {
	global $argv;
	if (empty($argv[$num_arg])){
		return false;
	}
	return $argv[$num_arg];
};


function utf8_encode_array($array){
	if (! is_array($array)){
		return utf8_encode($array);
	}
	$result = array();
	foreach ($array as $cle => $value) {
		$result[utf8_encode($cle)] = utf8_encode_array($value);
	}
	return $result;
}

function date_iso_to_fr($date){
	return date("d/m/Y",strtotime($date));
}

function time_iso_to_fr($datetime){
	return date("d/m/Y H:i:s",strtotime($datetime));
}

function date_fr_to_iso($date){
	return preg_replace("#^(\d{2})/(\d{2})/(\d{4})$#",'$3-$2-$1',$date);	
}

// For 4.3.0 <= PHP <= 5.4.0
if (!function_exists('http_response_code'))
{
	function http_response_code($newcode = NULL)
	{
		static $code = 200;
		if($newcode !== NULL)
		{
			header('X-PHP-Response-Code: '.$newcode, true, $newcode);
			if(!headers_sent())
				$code = $newcode;
		}
		return $code;
	}
}

function utf8_decode_array($array){
	if (! is_array($array)){
		return utf8_decode($array);
	}
	$result = array();
	foreach ($array as $cle => $value) {
		$result[utf8_decode($cle)] = utf8_decode_array($value);
	}
	return $result;
}

function isUTF8($filename)
{
	$info = finfo_open(FILEINFO_MIME_ENCODING);
	$type = finfo_buffer($info, file_get_contents($filename));
	finfo_close($info);

	return ($type == 'utf-8' || $type == 'us-ascii');
}


