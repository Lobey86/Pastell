<?php
require_once("init.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/transaction/TransactionSQL.class.php");

$recuperateur = new Recuperateur($_POST);

$id_t = $recuperateur->get('id_t');
$etat = $recuperateur->get('etat');

$transaction = new TransactionSQL($sqlQuery,$id_t);
$transaction->setEtat($etat);

$lastMessage->setLastMessage("La transaction $id_t est passé dans l'état $etat");

header("Location: " .SITE_BASE."flux/detail-transaction.php?id_t=$id_t");
