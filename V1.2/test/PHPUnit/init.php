<?php 

function pastell_autoload($class_name) {
	$result = @ include_once($class_name . '.class.php');
	if ( ! $result ){
		return false;
	}
	return true;
}
require_once(__DIR__."/../../init.php");

require_once 'PastellTestCase.class.php';