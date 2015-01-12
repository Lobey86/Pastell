<?php

function hecho($message,$quot_style=ENT_QUOTES){
	echo htmlentities($message,$quot_style,"iso-8859-15");
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
	if (! is_array($array) && !is_object($array)){
		return utf8_encode($array);
        }
	$result = array();
	foreach ($array as $cle => $value) {
		$result[utf8_encode($cle)] = utf8_encode_array($value);
	}
	return $result;
}

function exceptionToJson(Exception $ex) {
    $json = array(
        'date' => date('d/m/Y H:i:s'),
        'code' => $ex->getCode(),
        'file' => $ex->getFile(),
        'line' => $ex->getLine(),
        'message' => utf8_encode($ex->getMessage()),
        // utf8_encode_array non applicable sur getTrace() car peut contenir des "resources"
        'trace' => explode("\n", utf8_encode($ex->getTraceAsString()))
    );
    $json = json_encode($json);
    return $json;
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

function throwIfFalse($result, $message = false) {
    if ($result === false) {
        $this->throwLastError($message);
    }
    return $result;
}

function throwLastError($message = false) {
    $last = error_get_last();
    $cause = $last['message'];
    if ($message) {
        $ex = $message . ' Cause : ' . $cause;
    } else {
        $ex = $cause;
    }
    throw new Exception($ex);
}
