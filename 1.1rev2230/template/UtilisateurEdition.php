<?php if ($id_u) : ?>
<a href='utilisateur/detail.php?id_u=<?php echo $id_u ?>'>« Revenir à <?php echo $infoUtilisateur['prenom']." ". $infoUtilisateur['nom']?></a>
<?php elseif ($id_e) : ?>
<a href='entite/detail.php?id_e=<?php echo $id_e ?>'>« Revenir à <?php echo $infoEntite['denomination'] ?></a>
<?php else : ?>
<a href='entite/detail.php?id_e=<?php echo $id_e ?>'>« Revenir à la liste des utilisateurs globaux</a>

<?php endif;?>
<br/><br/>


<div class="box_contenu clearfix">


<form class="w700" action='utilisateur/edition-controler.php' method='post' enctype='multipart/form-data'>
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
	<th><label for='nom'>Nom<span>*</span></label> </th>
	<td> <input type='text' name='nom' value='<?php echo $infoUtilisateur['nom']?>'/></td>
</tr>
<tr>
	<th><label for='prenom'>Prénom<span>*</span></label> </th>
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

<?php 
$tabEntite = $roleUtilisateur->getEntite($this->Authentification->getId(),'entite:edition');
$entiteListe = new EntiteListe($sqlQuery);


?>
<tr>
	<th>Entité de base</th>
	<td>
		<select name='id_e'>
			<option value=''>...</option>
			<?php foreach($arbre as $entiteInfo): ?>
			<option value='<?php echo $entiteInfo['id_e']?>' <?php echo $entiteInfo['id_e']==$infoUtilisateur['id_e']?"selected='selected'":""?>>
				<?php for($i=0; $i<$entiteInfo['profondeur']; $i++){ echo "&nbsp&nbsp;";}?>
				|_<?php echo $entiteInfo['denomination']?> </option>
			<?php endforeach ; ?>
		</select>
	</td>
</tr>

</table>

	<div class="align_right">
	<input type='submit' class='submit' value="<?php echo $id_u?"Modification":"Création" ?>" />
	</div>

</form>
</div>
