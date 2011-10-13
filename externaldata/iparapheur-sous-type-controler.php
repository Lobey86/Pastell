<?php
require_once( PASTELL_PATH . "/externaldata/lib/IParapheurType.class.php");

$iparapheurtype = $recuperateur->getInt('iparapheurtype',0);


$iParapheurType= new IParapheurType();
$iParapheurType->setSousType($iparapheurtype,$sqlQuery,$donneesFormulaireFactory,$id_d,$id_e,$page,$field,$type);
