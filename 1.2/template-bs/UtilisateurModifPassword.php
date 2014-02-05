<a href='utilisateur/moi.php' class="btn btn-mini"><i class="icon-circle-arrow-left"></i>Espace utilisateur</a>


<div class="box">

<h2>Modifier votre mot de passe</h2>
<form action='utilisateur/modif-password-controler.php' method='post' >
<table class="table table-striped">

<tr>
<th class="w300">Ancien mot de passe : </th>
<td><input type='password' name='old_password'/></td>
</tr>

<tr>
<th>Nouveau mot de passe : </th>
<td><input type='password' name='password'/></td>
</tr>

<tr>
<th>Confirmer le nouveau mot de passe : </th>
<td><input type='password' name='password2'/></td>
</tr>


</table>
<input type='submit' class="btn" value='Modifier le mot de passe' />
</form>

</div>
