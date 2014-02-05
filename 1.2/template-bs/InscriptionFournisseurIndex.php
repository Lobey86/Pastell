<div class="w600">

	<div class="alert alert-info">
	L'inscription sur la plateforme Pastell vous permettra de télétransmettre des documents à toutes les collectivités inscrites
	</div>


	<div class="box">

	<h2>Formulaire d'inscription</h2>
	<p>Veuillez remplir le formulaire ci-dessous afin de pouvoir vous inscrire.</p>


	<form action='inscription/fournisseur/inscription-controller.php' method='post'>
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
		<td><input type='text' name='email' value='<?php echo $this->LastError->getLastInput('email')?>'/></td>
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
		 <td><input type='text' name='denomination' value='<?php echo $this->LastError->getLastInput('denomination')?>' /></td>
	</tr>
	<tr>
		<th><label for='siren'>
		numéro SIREN
		<span class='obl'>*</span></label> </th>
		 <td><input type='text' name='siren' value='<?php echo $this->LastError->getLastInput('siren')?>' /></td>
	</tr>

	</table>

		<input type='submit' class='btn' value="Soumettre la demande d'inscription" />

	</form>
	</div>
	<p class='align_center'><a href='<?php echo SITE_BASE ?>'>J'ai déjà un compte et je souhaite me connecter</a></p>
	<br/>
	<?php $this->render("SirenBox"); ?>

</div>
