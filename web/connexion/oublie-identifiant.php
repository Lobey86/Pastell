<?php
require_once(dirname(__FILE__)."/../init.php");

$page="oublie_identifiant";
$page_title="Oublie des identifiants";


include( PASTELL_PATH ."/include/haut.php");
?>

<div class="w500">

<?php include (PASTELL_PATH."/include/bloc_message.php"); ?>


<div class="box_contenu clearfix">
	<div class="box_connexion">
		<h2>Merci d'indiquer une information</h2>
		
		<div class='box_info'>
		Afin que nous puissions permettre la réinitialisation du mot de passe, 
		veuillez indiquer l'une des deux informations suivantes :
		</div>
		
		<form action='connexion/oublie-identifiant-controler.php' method='post' >
		<table>
		<tr>
		<th class="w50pc"><label for="login">Votre identifiant</label></th>
		<td class="w50pc"><input type="text" name="login" id="login" autocomplete='off'/></td>
		</tr>
			<tr>
		<th class="w50pc"><label for=""email""><b>OU</b> Votre email</label></th>
		<td class="w50pc"><input type="text" name="email" id="email" autocomplete='off'/></td>
		</tr>
		</table>
		
		<div class="float_left">
		<a href="connexion/connexion.php">Retourner à la connexion</a>
		</div>
		
		<div class="align_right">
		<input type="submit" value="Envoyer" class="submit" />
		</div>
		
		</form>
	
	</div>
</div>
</div>
<?php include( PASTELL_PATH ."/include/bas.php");
