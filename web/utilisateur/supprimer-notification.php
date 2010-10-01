<?php

require_once(dirname(__FILE__)."/../init-authenticated.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/notification/Notification.class.php");


$recuperateur = new Recuperateur($_POST);
$id_n = $recuperateur->get('id_n');


$notification = new Notification($sqlQuery);
$infoNotification = $notification->getInfo($id_n);

$id_u = $infoNotification['id_u'];

$notification->remove($id_n);

header("Location: detail.php?id_u=$id_u");
