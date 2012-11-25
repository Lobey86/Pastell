<?php 
require_once("../../init.php");

$recuperateur = new Recuperateur($_GET);

if (empty($_SESSION['id_u'])){
	header("Location: ".SITE_BASE."connexion/connexion.php");
	exit;
}

$page_title = "Inscription en cours de finalisation";

include( PASTELL_PATH ."/include/haut.php");

$utilisateur = new Utilisateur($sqlQuery);
$infoUtilisateur = $utilisateur->getInfo($_SESSION['id_u']);
?>
<div>
<p>Vous devez cliquez sur le lien du mail qui a été envoyé à :
 <b><?php echo $infoUtilisateur['email']; ?></b></p>

<p>
Vous pouvez également saisir le code qui vous a été envoyé dans le mail : 
</p>
<form action='inscription/fournisseur/mail-validation-controler.php' method='get' >
	<input type='text' name='chaine_verif' value='' />
</form>

<br/>

<p>Si ce n'est pas la bonne adresse email, vous pouvez <a href='inscription/fournisseur/desincription.php'>recommencer la procédure</a> </p>

<p>Nous pouvons également <a href='inscription/fournisseur/renvoie-mail-inscription.php'>renvoyer le mail</a></p>
</div>
<?php 
include( PASTELL_PATH ."/include/bas.php");
