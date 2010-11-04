<?php 
require_once(dirname(__FILE__)."/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/base/Certificat.class.php");


$recuperateur = new Recuperateur($_GET);
$id_e = $recuperateur->get('id_e');
$id_u = $recuperateur->get('id_u');


$infoUtilisateur = array('login' =>  $lastError->getLastInput('login'),
					'nom' =>  $lastError->getLastInput('nom'),
					'prenom' =>  $lastError->getLastInput('prenom'),
					'email'=> $lastError->getLastInput('email'),
					'certificat' => '',
);


$entite = new Entite($sqlQuery,$id_e);
$infoEntite = $entite->getInfo();
if ($id_u){
	$utilisateur = new Utilisateur($sqlQuery,$id_u);
	$infoUtilisateur = $utilisateur->getInfo();
}

if (! $infoEntite && ! $infoUtilisateur){
	header("Location: ".SITE_BASE . "index.php");
	exit;
}

$certificat = new Certificat($infoUtilisateur['certificat']);


$roleDroit = new RoleDroit();


if ($id_e){
	$page_title = "Nouvel utilisateur ";
}
if ($id_u){
	$page_title = "Modification de " .  $infoUtilisateur['prenom']." ". $infoUtilisateur['nom'];
	
}

include( PASTELL_PATH ."/include/haut.php");
?>
<?php if ($id_e) : ?>
<a href='entite/detail.php?id_e=<?php echo $id_e?>'>« Revenir à <?php echo $infoEntite['denomination']?></a>
<?php endif;?>
<?php if ($id_u) : ?>
<a href='utilisateur/detail.php?id_u=<?php echo $id_u ?>'>« Revenir à <?php echo $infoUtilisateur['prenom']." ". $infoUtilisateur['nom']?></a>
<?php endif;?>
<br/><br/>

<?php include (PASTELL_PATH."/include/bloc_message.php"); ?>

<div class="box_contenu clearfix">

<h2>
Veuillez remplir le formulaire ci-dessous afin de pouvoir créer un nouvel utilisateur.
</h2>

<form class="w700" action='utilisateur/edition-controler.php' method='post' enctype='multipart/form-data'>
<input type='hidden' name='id_e' value='<?php echo $id_e?>'>
<input type='hidden' name='id_u' value='<?php echo $id_u?>'>

<table>
<tr>
	<th><label for='login'>
	Identifiant (login)
	<span>*</span></label> </th>
	 <td> <input type='text' name='login' value='<?php echo $infoUtilisateur['login'] ?>' /></td>
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
	<td> <input type='text' name='email' value='<?php echo $infoUtilisateur['email']?>'/></td>
</tr>
<tr>
	<th><label for='nom'>Nom</label> </th>
	<td> <input type='text' name='nom' value='<?php echo $infoUtilisateur['nom']?>'/></td>
</tr>
<tr>
	<th><label for='prenom'>Prénom</label> </th>
	<td> <input type='text' name='prenom' value='<?php echo $infoUtilisateur['prenom']?>'/></td>
</tr>
<tr>
	<th><label for='certificat'>Certificat (PEM)</label> </th>
	<td> <input type='file' name='certificat' /><br/>
	<?php if ($certificat->isValid()) : ?>
		<?php  echo $certificat->getFancy()?>&nbsp;-&nbsp;
		<a href='utilisateur/supprimer-certificat.php?id_u=<?php echo $id_u?>'>supprimer</a>
	<?php endif;?>
	</td>
</tr>


<?php if($id_e) : ?>
<tr>
	<th>Entité de base</th>
	<td> <a href='entite/detail.php?id_e=<?php echo $id_e?>'><?php echo $infoEntite['denomination']?> 
		</a>
		<?php if ($infoEntite['siren']) :?>(<?php echo $infoEntite['siren']?>)<?php endif;?></td>
</tr>

<tr>
	<th>Rôle </th>
	<td> 
		<select name='role'>
			<?php foreach($roleDroit->getAllRole() as $role => $lesDroits): ?>
			<option value='<?php echo $role?>'> <?php echo $role ?> </option>
			<?php endforeach ; ?>
		</select>
	 </td>
</tr>
<?php endif;?>
</table>

	<div class="align_right">
	<input type='submit' class='submit' value="<?php echo $id_e?"Inscription":"Modification" ?>" />
	</div>

</form>
</div>

<?php 
include( PASTELL_PATH ."/include/bas.php");


