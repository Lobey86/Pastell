<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");

if  (! $roleUtilisateur->hasDroit($authentification->getId(),'test:lecture',0)){
	header("Location: ".SITE_BASE . "/index.php");
	exit;
}

$page_title = "Test du système";

include( PASTELL_PATH ."/include/haut.php");


include (PASTELL_PATH."/include/bloc_message.php"); ?>

<?php if ( ENABLE_VERIF_ENVIRONNEMENT ) :  ?>

<a href='test/verif-environnement.php'>Vérifier l'environnement</a>
<br/><br/>
<?php endif; ?>
<div class="box_contenu clearfix">


<h2>Test du SignServer</h2>
<br/>
<b>URL du signServer : </b><a href='<?php echo SIGN_SERVER_URL ?>'><?php echo SIGN_SERVER_URL ?></a>
<br/>
<br/>

<form action='test/test-signserver.php'/>
	<input type='submit' value='Tester la signature'/>
</form>
<br/>
<br/>

</div>
<?php 
include( PASTELL_PATH ."/include/bas.php");
