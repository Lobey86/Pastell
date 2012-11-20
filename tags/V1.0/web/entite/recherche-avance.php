<?php

require_once( dirname(__FILE__) . "/../init-authenticated.php");


$page_title = "Recherche de fournisseurs";



include( PASTELL_PATH ."/include/haut.php");
?>

<div class="box_info">
<p><strong>Version de démonstration</strong></p>
<p>La recherche avancée est désactivée pour la version de démonstration<br/><br/>
<a href='<?php echo SITE_BASE?>/entite/fournisseur.php'>« revenir à la liste des fournisseurs</a>
 </p></div>
<?php 
include( PASTELL_PATH ."/include/bas.php");
