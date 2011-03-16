<?php

$libelle = $recuperateur->get('libelle');

$donneesFormulaire = $donneesFormulaireFactory->get($id_d,$type);
$donneesFormulaire->setData('grade',$libelle);

header("Location: edition.php?id_d=$id_d&id_e=$id_e&page=$page");