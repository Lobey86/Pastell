<?php



exec("/Users/eric/Logiciel/unoconv-master/unoconv -f pdf /var/folders/bp/0x43447x029dpz0yrtvzmygc0000gn/T/pastell_tmp_folder_263261074/201404-pastell-roadmap.odt",$output,$return_var);

print_r($output);

echo $return_var;