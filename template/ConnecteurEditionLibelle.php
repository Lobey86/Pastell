

<a href='connecteur/edition.php?id_ce=<?php echo $connecteur_entite_info['id_ce'] ?>'>« Revenir à la définition du connecteur</a>
<br/><br/>
<div class="box_contenu clearfix">
<h2>Connecteur <?php hecho($connecteur_entite_info['type']) ?> - <?php hecho($connecteur_entite_info['id_connecteur'])?> : <?php hecho($connecteur_entite_info['libelle']) ?> 
</h2>
<br/><br/>

<form class="w700" action='connecteur/edition-libelle-controler.php' method='post' >
	<input type='hidden' name='id_ce' value='<?php echo $connecteur_entite_info['id_ce'] ?>' />
<table >

<tr>
<th>Libellé</th>
<td><input type='text' name='libelle' value='<?php hecho($connecteur_entite_info['libelle']) ?>'/></td>
</tr>

</table>
	
	<input type='submit' value='Modifier le libellé'/>
</form>

</div>
