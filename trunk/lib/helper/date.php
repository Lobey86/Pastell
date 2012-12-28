<?php


function date_iso_to_fr($date){
	return date("d/m/Y",strtotime($date));
}

function time_iso_to_fr($datetime){
	return date("d/m/Y H:i:s",strtotime($datetime));
}

function date_fr_to_iso($date){
	return preg_replace("#^(\d{2})/(\d{2})/(\d{4})$#",'$3-$2-$1',$date);	
}