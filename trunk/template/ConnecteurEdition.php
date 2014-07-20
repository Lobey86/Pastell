<a class='btn btn-mini' href='entite/detail.php?id_e=<?php echo $connecteur_entite_info['id_e']?>&page=3'><i class='icon-circle-arrow-left'></i>Revenir à <?php echo $entite_info['denomination']?></a>

<div class="box">
<h2>Connecteur <?php hecho($connecteur_entite_info['type']) ?> - <?php hecho($connecteur_entite_info['id_connecteur'])?> : <?php hecho($connecteur_entite_info['libelle']) ?> 
&nbsp;<a class='btn btn-mini' href="connecteur/edition-modif.php?id_ce=<?php hecho($id_ce) ?>">
Modifier
</a>
</h2>
<?php 

$this->render("DonneesFormulaireDetail");
 
$action_possible = $objectInstancier->ActionPossible->getActionPossibleOnConnecteur($id_ce,$authentification->getId());
 
foreach($action_possible as $action_name) : ?>
<form action='connecteur/action.php' method='post' style='margin-top:10px;'>
	<input type='hidden' name='id_ce' value='<?php echo $id_ce ?>' />
	<input type='hidden' name='action' value='<?php echo $action_name ?>' />
	<input type='submit' class='btn' value='<?php hecho($action->getActionName($action_name)) ?>'/>
</form>
<?php endforeach;?>

</div>



<div class="box">
<h2>Autres opérations</h2>

<a class='btn' href="connecteur/edition-libelle.php?id_ce=<?php echo $id_ce?>" >
Modifier le libellé de l'instance du connecteur (<?php hecho($connecteur_entite_info['libelle'])?>)
</a>&nbsp;&nbsp;
<a class='btn btn-danger' href="connecteur/delete.php?id_ce=<?php echo $id_ce?>" >
Supprimer ce connecteur 
</a>

</div>