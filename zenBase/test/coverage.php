<?php

require_once("../lib/ZenTest.class.php");

$zenTest = new ZenTest(dirname(__FILE__));
$zenTest->addToIncludePath(dirname(__FILE__)."/../lib");

$zenTest->coverage();
header("location: report");
