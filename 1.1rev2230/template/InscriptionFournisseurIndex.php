<div class="box_info">
<p>
L'inscription sur la plateforme Pastell vous permettra de télétransmettre des documents à toutes les collectivités inscrites
</p>
</div>


<div class="box_contenu clearfix">

<h2>Formulaire d'inscription</h2>
<p>Veuillez remplir le formulaire ci-dessous afin de pouvoir vous inscrire.</p>


<form  action='inscription/fournisseur/inscription-controller.php' method='post'>
<table>
<tr>
	<th class="w50pc"><label for='login'>
	Identifiant (login)
	<span>*</span></label> </th>
	 <td class="w50pc"><input type='text' name='login' value='<?php echo $this->LastError->getLastInput('login')?>' /></td>
</tr>
<tr>
	<th><label for='password'>
	Mot de passe
	<span>*</span></label> </th>
	 <td><input type='password' name='password' value='' /></td>
</tr>
<tr>
	<th><label for='password2'>
	Mot de passe (vérification)
	<span>*</span></label> </th>
	 <td><input type='password' name='password2' value='' /></td>
</tr>
<tr>
	<th><label for='email'>Email<span>*</span></label> </th>
	<td><input type='text' name='email' value='<?php echo $this->LastError->getLastInput('email')?>'/></td>
</tr>
<tr>
	<th><label for='nom'>
	Nom
	<span>*</span></label> </th>
	 <td><input type='text' name='nom' value='<?php echo $this->LastError->getLastInput('nom')?>' /></td>
</tr>
<tr>
	<th><label for='prenom'>
	Prénom
	<span>*</span></label> </th>
	 <td><input type='text' name='prenom' value='<?php echo $this->LastError->getLastInput('prenom')?>' /></td>
</tr>

<tr>
	<th><label for='denomination'>
	Raison sociale
	<span>*</span></label> </th>
	 <td><input type='text' name='denomination' value='<?php echo $this->LastError->getLastInput('denomination')?>' /></td>
</tr>
<tr>
	<th><label for='siren'>
	numéro SIREN
	<span>*</span></label> </th>
	 <td><input type='text' name='siren' value='<?php echo $this->LastError->getLastInput('siren')?>' /></td>
</tr>

</table>

	<div class="align_right">
	<input type='submit' class='submit' value="Soumettre la demande d'inscription" />
	</div>

</form>
</div>
<p><a href='<?php echo SITE_BASE ?>'>« J'ai déjà un compte et je souhaite me connecter</a></p>

<?php $this->render("SirenBox"); ?>

