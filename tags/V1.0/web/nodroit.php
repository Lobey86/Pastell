<?php
require_once(dirname(__FILE__)."/init-authenticated.php");

$page_title = "Aucun droit";

include( PASTELL_PATH ."/include/haut.php");
?>
<div class="box_error">
	<p>
		Vous n'avez aucun droit sur cette plateforme, veuillez contacter votre administrateur PASTELL.
	</p>
</div>
<?php 
include( PASTELL_PATH ."/include/bas.php");
