<div class="box">
<h2>Upstart</h2>
Dernier lancement du script action-automatique : <?php echo $last_upstart; ?>

<br/><br/>
<a href='system/reload-upstart.php?num=15' class='btn btn-warning'>Terminer le script (SIGTERM)</a>
<a href='system/reload-upstart.php?num=9' class='btn btn-danger'>Tuer le script (SIGKILL)</a>


</div>

<div class="box">
<h2>Dernières actions automatique</h2>

<?php 
$this->SuivantPrecedent($offset,$limit,$count,"system/index.php?page_number=0");
?>


<table class="table table-striped">
<tr>
	<th>Entité</th>
	<th>Document</th>
	<th>Etat actuel</th>
	<th>Etat cible</th>
	<th>Premier essai</th>
	<th>Dernier essai</th>
	<th>Nombre d'essais</th>
	<th>Message</th>
	
</tr>
<?php foreach($all_log as $i => $log) : ?>
	<tr>
		<td><?php hecho($log['denomination'])?></td>
		<td><a href='document/detail.php?id_e=<?php echo $log['id_e']?>&id_d=<?php hecho($log['id_d'])?>'><?php hecho($log['titre'])?:$log['id_d']?> </a></td>
		<td><?php echo $log['etat_source']?></td>
		<td><?php echo $log['etat_cible']?></td>
		<td><?php hecho($log['first_try'])?></td>
		<td><?php hecho($log['last_try'])?></td>
		<td><?php hecho($log['nb_try'])?> </td>
		<td><?php hecho($log['last_message'])?> </td>
	</tr>
<?php endforeach;?>

</table>

<a href='system/nettoyer-action-auto.php' class='btn btn-warning'>Nettoyer les actions terminées</a>
</div>

