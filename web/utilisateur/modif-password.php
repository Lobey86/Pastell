<?php 
require_once(dirname(__FILE__)."/../init-authenticated.php");

$id_u = $authentification->getId();

$page_title="Modification de votre mot de passe";

include( PASTELL_PATH ."/include/haut.php");

$lienRetourHTML->display("Espace utilisateur","utilisateur/moi.php");
?><?php include (PASTELL_PATH."/include/bloc_message.php"); ?>

<div class="box_contenu clearfix">

<h2>Modifier votre mot de passe</h2>
<form  action='utilisateur/modif-password-controler.php' method='post' >
<table >

<tr>
<th>Ancien mot de passe : </th>
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
<input type='submit' value='Modifier le mot de passe' />
</form>

</div>
<?php 
include( PASTELL_PATH ."/include/bas.php");


