<?php


function date_iso_to_fr($date){
	return date("d/m/Y",strtotime($date));
}

function time_iso_to_fr($datetime){
	return date("d/m/Y H:i:s",strtotime($datetime));
}