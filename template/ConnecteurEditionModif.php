
<a href='connecteur/edition.php?id_ce=<?php echo $id_ce?>'>« Revenir à <?php echo $connecteur_entite_info['libelle']?></a>
<br/><br/>
<div class="box_contenu clearfix">
<h2>Connecteur <?php hecho($connecteur_entite_info['type']) ?> - <?php hecho($connecteur_entite_info['id_connecteur'])?> : <?php hecho($connecteur_entite_info['libelle']) ?> 
</h2>
<?php 

$afficheurFormulaire->affiche(0,"connecteur/edition-modif-controler.php",
									"connecteur/recuperation-fichier.php?id_ce=$id_ce",
									"connecteur/supprimer-fichier.php?id_ce=$id_ce",
									"connecteur/external-data.php"); 

?></div>