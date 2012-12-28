<div class='lien_retour'>
	<a href='utilisateur/moi.php'>« Espace utilisateur</a>
</div>
<div class="box_contenu clearfix">

<h2>Modifier votre email</h2>
<form class="w700"  action='utilisateur/modif-email-controler.php' method='post' >
<table >


<tr>
<th>Email actuel: </th>
<td><?php hecho($utilisateur_info['email'])?></td>
</tr>


<tr>
<th>Nouvel email : </th>
<td><input type='text' name='email' value='<?php echo $this->LastError->getLastInput('email')?>'/></td>
</tr>

<tr>
<th>Votre mot de passe : </th>
<td><input type='password' name='password'/></td>
</tr>

</table>
<input type='submit' value='Modifier le mot de passe' />
</form>

</div>
