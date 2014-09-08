<div class="w700">


<?php if ($message_connexion) : ?>
<div class="alert">
	<?php echo nl2br(htmlentities($message_connexion))?>
</div>
<?php endif;?>


<?php 

$fluxDefinitionFiles = $objectInstancier->FluxDefinitionFiles;

$certificatConnexion = new CertificatConnexion($sqlQuery);
$id_u = $certificatConnexion->autoConnect();
	
if ($id_u ): 
$utilisateur = new Utilisateur($sqlQuery);
$utilisateurInfo = $utilisateur->getInfo($id_u);
?>
<div class="box">
	<h2>Connexion automatique</h2>

	Votre certificat vous permet de automatiquement avec le compte 
	<a href='connexion/autoconnect.php'><?php echo $utilisateurInfo['login'] ?></a>

</div>
<?php endif;?>

<div class="box">
		<h2>Merci de vous identifier</h2>
		
		<form class="form-horizontal" action='connexion/connexion-controler.php' method='post'>
			<div class="control-group">
				<label class="control-label" for="login">Identifiant</label>
				<div class="controls">
					<input type="text" name="login" id="login" class='noautocomplete' autocomplete="off" placeholder="Identifiant" />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="password">Mot de passe</label>
				<div class="controls">
					<input type="password" name="password" id="password" placeholder="Mot de passe" />
				</div>

			</div>
			
			<div class="align_right">
				<button type="submit" class="btn" /><i class="icon-user"></i>Connexion</button>
			</div>
		</form>
		<hr/>
		<div class="align_center">
		<a href="connexion/oublie-identifiant.php">J'ai oublié mes identifiants</a>
		</div>
</div>



<?php if ($this->DocumentTypeFactory->isSuperTypePresent('Flux Fournisseur')) : ?>

<div class="box">
	<h2>Nouveau compte</h2>
	<hr/>
		<div class="float_left">
		Créer un compte fournisseur :
		</div>
		<div class="align_right">
		<a class="btn" href="<?php echo SITE_BASE ?>inscription/fournisseur/index.php"><i class="icon-ok-sign"></i>Nouveau compte</a>
		</div>
			<br/>
		<div class="float_left">
		Créer un compte citoyen :
		</div>
		<div class="align_right">
		<a class="btn" href="<?php echo SITE_BASE ?>inscription/citoyen/index.php"><i class="icon-ok-sign"></i>Nouveau compte</a>
		</div>
	
</div>
<?php endif; ?>


<?php if (! defined("PRODUCTION")) : ?>
<div class="box">
<h2>Version de démonstration</h2>


<div class="alert alert-info">
<p>Vous êtes sur la version de démonstration de Pastell.</p>
<p>Utilisez un des comptes suivants pour vous connecter.</p>
</div>

<table class="table table-striped">
	<tr>
		<th>Rôle</th>
		<th>Identifiant</th>
		<th>Mot de passe</th>
	</tr>
	<tr>
		<td>Super administrateur</td>
		<td>admin</td>
		<td>admin</td>
	</tr>
	<tr>
		<td>Fournisseur</td>
		<td>fournisseur1</td>
		<td>fournisseur1</td>
	</tr>
	<tr>
		<td>Utilisateur collectivité</td>
		<td>col1</td>
		<td>col1</td>
	</tr>
	<tr>
		<td>Centre de gestion</td>
		<td>cdg1</td>
		<td>cdg1</td>
	</tr>
</table>
</div>

<?php endif;?>

</div>
