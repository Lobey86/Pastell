<div class="box_contenu clearfix">
<h2>Upstart</h2>
Dernier lancement du script action-automatique : <?php echo $last_upstart; ?> 
<br/><br/>
<h2>Dernières actions automatique</h2>

<?php 
suivant_precedent($offset,$limit,$count,"system/index.php?page_number=0");
?>


<table class="tab_01"><tbody>
<tr>
	<th>Entité</th>
	<th>Document</th>
	<th>Etat actuel</th>
	<th>Etat cible</th>
	<th>Premier essai</th>
	<th>Dernier essai</th>
	<th>Nombre d'essais</th>
	<th>Messages</th>
	
</tr>
<?php foreach($all_log as $i => $log) : ?>
	<tr class='<?php echo $i%2?'bg_class_gris':'bg_class_blanc'?>'>
		<td><?php hecho($log['denomination'])?></td>
		<td><a href='document/detail.php?id_e=<?php echo $log['id_e']?>&id_d=<?php hecho($log['id_d'])?>'><?php hecho($log['titre'])?:$log['id_d']?> </a></td>
		<td><?php echo $log['etat_source']?></td>
		<td><?php echo $log['etat_cible']?></td>
		<td><?php hecho($log['first_try'])?></td>
		<td><?php hecho($log['last_try'])?></td>
		<td><?php hecho($log['nb_try'])?> </td>
		<td><a href='system/message.php?id_e=<?php echo $log['id_e']?>&id_d=<?php echo $log['id_d']?>'>voir</a></td>
	</tr>
<?php endforeach;?>
</tbody>
</table>


</div>