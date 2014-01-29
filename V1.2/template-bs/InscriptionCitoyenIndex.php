<div class='w600'>

<div class="alert alert-info">
L'inscription sur la plateforme Pastell vous permettra d'envoyer et d'assurer le suivie de vos communications avec les collectivités inscrites.
</div>

<div class="box">

<h2>Formulaire d'inscription</h2>
<p>Veuillez remplir le formulaire ci-dessous afin de pouvoir vous inscrire.</p>


<form action='inscription/citoyen/inscription-controller.php' method='post'>
<table class='table table-striped'>
<tr>
	<th><label for='email'>Email<span class='obl'>*</span></label> </th>
	<td><input type='text' name='email' value='<?php echo $this->LastError->getLastInput('email')?>'/></td>
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
</table>


	<input type='submit' class='btn' value="S'inscrire" />


</form>
</div>
<p class='align_center'><a href='<?php echo SITE_BASE ?>'>J'ai déjà un compte et je souhaite me connecter</a></p>

</div>