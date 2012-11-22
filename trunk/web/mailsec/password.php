<?php
require_once( dirname(__FILE__) . "/../init.php");
require_once( PASTELL_PATH . '/lib/formulaire/AfficheurFormulaire.class.php');

$recuperateur = new Recuperateur($_GET);
$key = $recuperateur->get('key');

$documentEmail = new DocumentEmail($sqlQuery);
$info  = $documentEmail->getInfoFromKey($key);

if (! $info ){
	header("Location: invalid.php");
	exit;
}

$page= "Mail sécurisé";
$page_title= " Mail sécurisé";

include( PASTELL_PATH ."/include/haut.php");
?>
<?php include(PASTELL_PATH . "/include/bloc_message.php");?>

<div class="box_contenu">
	Ce message est protégé par un mot de passe :
	<form action='mailsec/password-controler.php' method='post'>
		<input type='hidden' name='key' value='<?php echo $key?>' />
		<input type='password' name='password' />
		<input type='submit' />
	</form>	

</div>
<?php 
include( PASTELL_PATH ."/include/bas.php");

