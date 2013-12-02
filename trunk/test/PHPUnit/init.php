<?php 

function pastell_autoload($class_name) {
	require_once($class_name . '.class.php');
}
require_once(__DIR__."/../../init.php");

require_once 'PastellTestCase.class.php';