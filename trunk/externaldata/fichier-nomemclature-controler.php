<?php

$fieldValue = $recuperateur->get($field);

$donneesFormulaire = $objectInstancier->ConnecteurFactory->getConnecteurConfig($id_ce);
$donneesFormulaire->setData($field,$fieldValue);

header("Location: edition-modif.php?id_ce=$id_ce");

