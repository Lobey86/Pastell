<?php


$fieldValue = $recuperateur->get($field);


$donneesFormulaire = $donneesFormulaireFactory->getEntiteFormulaire($id_e);
$donneesFormulaire->setData($field,$fieldValue);

header("Location: edition-properties.php?id_e=$id_e&page=$page");
exit;
