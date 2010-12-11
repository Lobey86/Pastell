<?php

require_once("init.php");


if (! $authentification->isConnected()){
	
		header("Location: " . SITE_BASE ."connexion/connexion.php");
		exit;
}

