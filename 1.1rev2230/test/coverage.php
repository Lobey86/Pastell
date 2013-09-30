<?php

require_once("ZenTest.class.php");

$zenTest = new ZenTest(dirname(__FILE__));
$zenTest->addToIncludePath(dirname(__FILE__)."/../lib");

$zenTest->coverage();
header("location: report");
