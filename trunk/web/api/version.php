<?php
require_once("init-api.php");

$infoVersionning = $versionning->getAllInfo();
$infoVersionning['version_complete'] = $infoVersionning['version-complete']; 
$JSONoutput->display($infoVersionning);