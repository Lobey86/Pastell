<?php

require_once( __DIR__ . "/../../web/init.php");
require_once(__DIR__ . "/../../pastell-core/MailTo.class.php");
require_once(__DIR__ . "/BLBatch.class.php");

$blbatch = new BLBatch();

$destinataire = $blbatch->read('Destinataires (séparés par \',\' si plusieurs, BLESDEV par défaut)', 'blesdev@berger-levrault.fr');
$objet = $blbatch->read('Sujet du mail', '[BUS BL] Mail envoyé depuis ' . FQDN);
$contenu = $blbatch->read('Contenu du message', 'Message par défaut.');

$mailto = new MailTo($objectInstancier);
$mailto->mail($destinataire, $objet, $contenu, '');
