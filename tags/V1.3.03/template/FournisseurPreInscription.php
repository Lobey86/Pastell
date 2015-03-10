<?php 
if ($has_error) {
	return;	
}
?>
<div class="box w600">
		<h2>Inscription sur Pastell </h2>
		<p>Veuillez entrer les informations suivantes afin de pouvoir utiliser le service Pastell</p>
	<form class="form-horizontal" action='fournisseur/inscription-controler.php' method='post'>
		<input type='hidden' name='id_e' value='<?php echo $id_e?>'/>
		<input type='hidden' name='id_d' value='<?php echo $id_d?>'/>
		<input type='hidden' name='s' value='<?php echo $secret?>'/>
		<table class='table table-striped'>
	<tr>
		<th class="w50pc"><label for='login'>
		Identifiant (login)
		<span class='obl'>*</span></label> </th>
		 <td class="w50pc"><input type='text' name='login' value='<?php echo $this->LastError->getLastInput('login')?>' /></td>
	</tr>
	<tr>
		<th><label for='password'>
		Mot de passe
		<span class='obl'>*</span></label> </th>
		 <td><input type='password' name='password' value='' /></td>
	</tr>
	<tr>
		<th><label for='password2'>
		Mot de passe (vérification)
		<span class='obl'>*</span></label> </th>
		 <td><input type='password' name='password2' value='' /></td>
	</tr>
	<tr>
		<th><label for='email'>Email<span class='obl'>*</span></label> </th>
		<td><input type='text' name='email' value='<?php echo $this->LastError->getLastInput('email')?:$email?>'/></td>
	</tr>
	<tr>
		<th><label for='nom'>
		Nom
		<span class='obl'>*</span></label> </th>
		 <td><input type='text' name='nom' value='<?php echo $this->LastError->getLastInput('nom')?>' /></td>
	</tr>
	<tr>
		<th><label for='prenom'>
		Prénom
		<span class='obl'>*</span></label> </th>
		 <td><input type='text' name='prenom' value='<?php echo $this->LastError->getLastInput('prenom')?>' /></td>
	</tr>

	<tr>
		<th><label for='denomination'>
		Raison sociale
		<span class='obl'>*</span></label> </th>
		 <td><input type='text' name='denomination' value='<?php echo $this->LastError->getLastInput('denomination')?:$raison_sociale?>' /></td>
	</tr>
	<tr>
		<th><label for='siren'>
		numéro SIREN
		<span class='obl'>*</span></label> </th>
		 <td><input type='text' name='siren' value='<?php echo $this->LastError->getLastInput('siren')?>' /></td>
	</tr>

	</table>
	<button type="submit" class="btn" /><i class="icon-user"></i>Ouvrir un compte sur Pastell</button>
	</form>
</div>

<div class="box w600">
		<h2>Déjà inscrit sur Pastell ? </h2>
		<p>Veuillez saisir vos identifiants</p>
		
			<form class="form-horizontal" action='fournisseur/deja-inscrit-controler.php' method='post'>
		<input type='hidden' name='id_e' value='<?php echo $id_e?>'/>
		<input type='hidden' name='id_d' value='<?php echo $id_d?>'/>
		<input type='hidden' name='s' value='<?php echo $secret?>'/>
		<table class='table table-striped'>
	<tr>
		<th class="w50pc"><label for='login'>
		Identifiant (login)
		<span class='obl'>*</span></label> </th>
		 <td class="w50pc"><input type='text' name='login' value='<?php echo $this->LastError->getLastInput('login')?>' /></td>
	</tr>
	<tr>
		<th><label for='password'>
		Mot de passe
		<span class='obl'>*</span></label> </th>
		 <td><input type='password' name='password' value='' /></td>
	</tr>
	</table>
				<button type="submit" class="btn" /><i class="icon-user"></i>Connexion</button>
		
	</form>

</div>

<?php $this->render("SirenBox");?>
