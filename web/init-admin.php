<?php
require_once("init-authenticated.php");

if (! $authentification->isAdmin()){
	header("Location: " . SITE_BASE );
	exit;
}
