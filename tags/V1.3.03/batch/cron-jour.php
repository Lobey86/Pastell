#! /usr/bin/php
<?php

require_once( __DIR__ . "/../web/init.php");

$objectInstancier->NotificationMail->sendDailyDigest();