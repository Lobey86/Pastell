<?php
require_once(dirname(__FILE__)."/../init.php");
require_once( PASTELL_PATH . "/lib/authentification/CertificatConnexion.class.php");

$page="connexion";
$page_title="Connexion";

include( PASTELL_PATH ."/include/haut.php");
?>

<div class="w500">

<?php include (PASTELL_PATH."/include/bloc_message.php"); ?>

<?php 

$certificatConnexion = new CertificatConnexion($sqlQuery);
$id_u = $certificatConnexion->autoConnect();
	
if ($id_u ): 
$utilisateur = new Utilisateur($sqlQuery,$id_u);
$utilisateurInfo = $utilisateur->getInfo();
?>
<div class="box_contenu clearfix">
	<h2>Connexion automatique</h2>

	Votre certificat vous permet de automatiquement avec le compte 
	<a href='connexion/autoconnect.php'><?php echo $utilisateurInfo['login'] ?></a>

</div>
<?php endif;?>

<div class="box_contenu clearfix">
	<div class="box_connexion">
		<h2>Merci de vous identifier</h2>
		
		<form action='connexion/connexion-controler.php' method='post' >
		<table>
		<tr>
		<th class="w50pc"><label for="login">Identifiant</label></th>
		<td class="w50pc"><input type="text" name="login" id="login" class='noautocomplete'/></td>
		</tr>
		<tr>
		<th><label for="password">Mot de passe</label></th>
		<td><input type="password" name="password" id="password" /></td>
		</tr>
		</table>
		
		<div class="float_left">
		<a href="connexion/oublie-identifiant.php">J'ai oublié mes identifiants</a>
		<br/>
		<a href='http://www.creatic59.fr/dematerialisation/aide'>Aide</a>
		</div>
		
		<div class="align_right">
		<input type="submit" value="Connexion" class="submit" />
		</div>
		
		</form>
	
	</div>
</div>
<?php if ($documentTypeFactory->isTypePresent('Flux fournisseur')) : ?>

<div class="box_contenu clearfix">
	<h2>Nouveau compte</h2>
	<hr/>
		<div class="float_left">
		Créer un compte fournisseur :
		</div>
		<div class="align_right">
		<a class="btn" href="<?php echo SITE_BASE ?>inscription/fournisseur/index.php">Nouveau compte</a>
		</div>
			<br/>
		<div class="float_left">
		Créer un compte citoyen :
		</div>
		<div class="align_right">
		<a class="btn" href="<?php echo SITE_BASE ?>inscription/citoyen/index.php">Nouveau compte</a>
		</div>
	
</div>
<?php endif; ?>


<?php if (! defined("PRODUCTION")) : ?>
<div class="box_contenu clearfix">
<h2>Version de démonstration</h2>


<div class="box_alert">
<p>Vous êtes sur la version de démonstration de Pastell.</p>
<p>Utilisez un des comptes suivants pour vous connecter.</p>
</div>

<table class="tab_01">
	<tr>
		<th>Rôle</th>
		<th>Identifiant</th>
		<th>Mot de passe</th>
	</tr>
	<tr  class='bg_class_gris'>
		<td>Super administrateur</td>
		<td>admin</td>
		<td>admin</td>
	</tr>
	<tr class='bg_class_blanc'>
		<td>Fournisseur</td>
		<td>fournisseur1</td>
		<td>fournisseur1</td>
	</tr>
	<tr  class='bg_class_gris'>
		<td>Utilisateur collectivité</td>
		<td>col1</td>
		<td>col1</td>
	</tr>
	<tr  class='bg_class_blanc'>
		<td>Centre de gestion</td>
		<td>cdg1</td>
		<td>cdg1</td>
	</tr>
</table>
</div>

<?php endif;?>

</div>

<?php include( PASTELL_PATH ."/include/bas.php");
