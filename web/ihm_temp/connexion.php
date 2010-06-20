<?php 
$page = "connexion";
$page_title = "Connexion";
include ("incHaut.php");
?>

<div class="box_connexion">

<h2>Merci de vous identifier</h2>

<form>

<table>
<tr>
<th><label for="pseudo">Identifiant</label></th>
<td><input type="text" name="pseudo" id="pseudo" /></td>
</tr>
<tr>
<th><label for="pwd">Mot de passe</label></th>
<td><input type="text" name="pwd" id="pwd" /></td>
</tr>
</table>

<div class="align_right">
<input type="submit" value="Connexion" class="submit" />
</div>

</form>

<hr/>

<a href="#">J'ai oublié mes identifiants</a>

</div>

<?php include ("incBas.php") ?>