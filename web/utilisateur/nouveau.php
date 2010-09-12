<?php 
include( "../init-admin.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");

$page_title = "Nouvel utilisateur ";

$recuperateur = new Recuperateur($_GET);
$siren = $recuperateur->get('siren');

$entite = new Entite($sqlQuery,$siren);
$infoEntite = $entite->getInfo();

if (! $infoEntite){
	header("Location: ".SITE_BASE . "index.php");
}

include( PASTELL_PATH ."/include/haut.php");
?>


<?php include (PASTELL_PATH."/include/bloc_message.php"); ?>

<div class="box_contenu clearfix">

<h2>
Veuillez remplir le formulaire ci-dessous afin de pouvoir créer un nouvel utilisateur.
</h2>

<form class="w700" action='utilisateur/nouveau-controler.php' method='post'>
<input type='hidden' name='siren' value='<?php echo $siren?>'>
<table>
<tr>
	<th><label for='login'>
	Identifiant (login)
	<span>*</span></label> </th>
	 <td> <input type='text' name='login' value='<?php echo $lastError->getLastInput('login')?>' /></td>
</tr>
<tr>
	<th><label for='password'>
	Mot de passe
	<span>*</span></label> </th>
	 <td> <input type='password' name='password' value='' /></td>
</tr>
<tr>
	<th><label for='password2'>
	Mot de passe (vérification)
	<span>*</span></label> </th>
	 <td> <input type='password' name='password2' value='' /></td>
</tr>
<tr>
	<th><label for='email'>Email<span>*</span></label> </th>
	<td> <input type='text' name='email' value='<?php echo $lastError->getLastInput('email')?>'/></td>
</tr>
<tr>
	<th><label for='nom'>Nom</label> </th>
	<td> <input type='text' name='nom' value='<?php echo $lastError->getLastInput('nom')?>'/></td>
</tr>
<tr>
	<th><label for='prenom'>Prénom</label> </th>
	<td> <input type='text' name='prenom' value='<?php echo $lastError->getLastInput('prenom')?>'/></td>
</tr>
<tr>
	<th>numéro SIREN</th>
	<td> <?php echo $siren ?> (<?php echo $infoEntite['denomination']?>) </td>
</tr>


</table>

	<div class="align_right">
	<input type='submit' class='submit' value="Soumettre la demande d'inscription" />
	</div>

</form>
</div>

<?php 
include( PASTELL_PATH ."/include/bas.php");


