<?php
require_once(dirname(__FILE__)."/../init.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");


$recuperateur = new Recuperateur($_GET);

$mail_verif_password = $recuperateur->get('mail_verif');

$page="oublie_identifiant";
$page_title="Oublie des identifiants";


include( PASTELL_PATH ."/include/haut.php");
?>

<div class="w500">

<?php include (PASTELL_PATH."/include/bloc_message.php"); ?>


<div class="box_contenu clearfix">
	<div class="box_connexion">
		<h2>Réinitialisation du mot de passe</h2>
		
		
		<form action='connexion/changement-mdp-controler.php' method='post' >
		<input type='hidden' name='mail_verif_password' value='<?php echo $mail_verif_password?>'/>
		<table>
			<tr>
			<th><label for="password">Mot de passe</label></th>
			<td><input type="password" name="password" id="password" /></td>
			</tr>
			<tr>
			<th><label for="password2">Mot de passe (confirmer)</label></th>
			<td><input type="password" name="password2" id="password" /></td>
			</tr>
		</table>
		
		<div class="float_left">
		<a href="connexion/connexion.php">Retourner à la connexion</a>
		</div>
		
		<div class="align_right">
		<input type="submit" value="Modifier" class="submit" />
		</div>
		
		</form>
	
	</div>
</div>
</div>
<?php include( PASTELL_PATH ."/include/bas.php");
