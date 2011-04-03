<?php
require_once("init-api.php");

$infoVersionning = $versionning->getAllInfo();
$JSONoutput->display($infoVersionning);