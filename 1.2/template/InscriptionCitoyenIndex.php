<div class="box_info">
<p>
L'inscription sur la plateforme Pastell 
vous permettra d'envoyer et d'assurer le suivie 
de vos communications avec les collectivités inscrites.
</p>
</div>


<div class="box_contenu clearfix">

<h2>Formulaire d'inscription</h2>
<p>Veuillez remplir le formulaire ci-dessous afin de pouvoir vous inscrire.</p>


<form  action='inscription/citoyen/inscription-controller.php' method='post'>
<table>
<tr>
	<th><label for='email'>Email<span>*</span></label> </th>
	<td><input type='text' name='email' value='<?php echo $this->LastError->getLastInput('email')?>'/></td>
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
</table>

	<div class="align_right">
	<input type='submit' class='submit' value="S'inscrire" />
	</div>

</form>
</div>
<p><a href='<?php echo SITE_BASE ?>'>« J'ai déjà un compte et je souhaite me connecter</a></p>

