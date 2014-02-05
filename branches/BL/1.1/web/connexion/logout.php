<?php

require_once(dirname(__FILE__)."/../init.php");

$authentification->deconnexion();
header("Location: connexion.php");