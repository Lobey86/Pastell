<?php

$donneesFormulaire = $donneesFormulaireFactory->get($id_d,$type);

$classif = $recuperateur->get('classif');


$donneesFormulaire->setData('classification',$classif);


header("Location: edition.php?id_d=$id_d&id_e=$id_e&page=$page");
