<?php

require_once(dirname(__FILE__)."/../init-authenticated.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/notification/Notification.class.php");


$recuperateur = new Recuperateur($_POST);

$id_u = $recuperateur->get('id_u');
$id_e = $recuperateur->get('id_e',0);
$type = $recuperateur->get('type',0);


$notification = new Notification($sqlQuery);

$notification->add($id_u,$id_e,$type,0);

header("Location: detail.php?id_u=$id_u");