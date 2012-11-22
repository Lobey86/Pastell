<?php 

require_once("../lib/ZenTest.class.php");

$zenTest = new ZenTest(dirname(__FILE__));
$zenTest->run();

?>

<a href='coverage.php'>Voir la couverture de code</a>