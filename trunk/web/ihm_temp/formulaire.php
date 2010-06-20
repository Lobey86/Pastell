<?php
$page = "formulaire";
$page_title = "Test formulaire";
include ("incHaut.php");
?>

<h2>Formulaire</h2>


<form class="w700" action="#">

<table>

<tr>
<th><label for="pseudo">Identifiant<span>*</span></label></th>
<td><input type="text" name="pseudo" id="pseudo" /></td>
</tr>

<tr class="tr_box_error">
<th><label for="pwd">Mot de passe<span>*</span></label></th>
<td><p>Message d'erreur ici entre deux balises P</p><input type="text" name="pwd" id="pwd" /></td>
</tr>

<tr>
<th><label for="c1">Textarea Textarea Textarea </label></th>
<td><textarea rows="5" cols=""  name="c1" id="c1"></textarea></td>
</tr>

<tr>
<th><label for="c2">Lise à séléctionner</label></th>
<td>
<select name="c2" id="c2">
<option value="0">Option 0</option>
<option value="1">Option 1</option>
</select>
</td>
</tr>

<tr>
<th><label for="c3">Case à cocher<span>*</span></label></th>
<td><input type="checkbox" name="c3" id="c3" /></td>
</tr>

</table>

<div class="align_right">
<input type="submit" value="Connexion" class="submit" />
</div>

</form>




<?php include ("incBas.php") ?>