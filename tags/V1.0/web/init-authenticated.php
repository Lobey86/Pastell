<?php

require_once("init.php");

require_once(PASTELL_PATH . "/lib/droit/DroitChecker.class.php");

if (! $authentification->isConnected()){
	
		header("Location: " . SITE_BASE ."connexion/connexion.php");
		exit;
}

$droitChecker = new DroitChecker($roleUtilisateur,$authentification->getId());