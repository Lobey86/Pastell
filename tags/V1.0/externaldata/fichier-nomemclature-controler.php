<?php


$fieldValue = $recuperateur->get($field);


$donneesFormulaire = $donneesFormulaireFactory->get($id_e,'collectivite-properties');
$donneesFormulaire->setData($field,$fieldValue);

header("Location: edition-properties.php?id_e=$id_e&page=$page");
exit;
