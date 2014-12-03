

<a href='connecteur/edition.php?id_ce=<?php echo $connecteur_entite_info['id_ce'] ?>'>« Revenir à la définition du connecteur</a>
<br/><br/>
<div class="box_contenu clearfix">
<h2>Connecteur <?php hecho($connecteur_entite_info['type']) ?> - <?php hecho($connecteur_entite_info['id_connecteur'])?> : <?php hecho($connecteur_entite_info['libelle']) ?> 
</h2>
<br/><br/>
<div class='box_alert'>
<p>Attention, la suppression du connecteur est irréversible!</p>
</div>
<br/><br/>
<form action='connecteur/delete-controler.php' method='post' >
	<input type='hidden' name='id_ce' value='<?php echo $connecteur_entite_info['id_ce'] ?>' />
	<input type='submit' value='Supprimer le connecteur'/>
</form>

</div>