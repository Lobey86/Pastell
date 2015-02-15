
<a class='btn btn-mini' href='journal/index.php?id_e=<?php echo $id_e?>&id_d=<?php echo $id_d?>&type=<?php echo $type?>&offset=<?php echo $offset ?>'><i class='icon-circle-arrow-left'></i>Retour au journal </a>

<div class="box">

<h2>Détail de l'évenement <?php echo $id_j ?></h2>

<table class="table table-striped">
<tr>
		<th class='w200'>Numéro</th>
		<td><?php echo $id_j ?></td>
</tr>
<tr>
		<th>Date</th>
		<td><?php echo time_iso_to_fr($info['date']) ?></td>
</tr>
<tr>		
		<th>Type</th>
		<td><?php echo $this->Journal->getTypeAsString($info['type']) ?></td>
</tr>
<tr>
		<th>Entité</th>
		<td><a href='entite/detail.php?id_e=<?php echo $info['id_e'] ?>'><?php echo  $info['denomination']?></a></td>
		</tr>
<tr>
		<th>Utilisateur</th>
		<td><a href='utilisateur/detail.php?id_u=<?php echo  $info['id_u']?>'><?php echo $info['prenom'] . " " . $info['nom']?></a>
		</td>
</tr>
<?php if($info['id_d']) :?>
<tr>
		<th>Document</th>
		<td>
			<a href='document/detail.php?id_d=<?php echo $info['id_d']?>&id_e=<?php echo $info['id_e']?>'> 
				<?php echo $info['titre']?>
			</a>
		</td>
</tr>

<tr>
	<th>Type de document</th>
	<td><?php echo $info['document_type_libelle']?> (<?php echo $info['document_type']?>)</td>
</tr>
<?php endif;?>
<tr>
	<th>Action</th>
	<td><?php echo $info['action_libelle']?> (<?php echo  $info['action']?>)</td>
</tr>
<tr>
	<th>Message</th>
	<td><?php echo  $info['message']?></td>
</tr>
<tr>
	<th>Message horodaté: </th>
	<td><?php echo  $info['message_horodate']?>
	<br/>
	<a href='journal/message.php?id_j=<?php echo $id_j ?>'>Télécharger</a>
	</td>
</tr>
<tr>
		<th>Date et heure de l'horodatage: </th>
		<td><?php echo  $info['date_horodatage']?></td>
</tr>
<tr>
		<th>Preuve </th>
		<td>
			<a href='journal/preuve.php?id_j=<?php echo $id_j?>'>Télécharger</a><br/><br/>
			<pre>
				<?php echo  $preuve_txt ?>
			</pre>		
		</td>
</tr>
<tr>
		<th>Vérification</th>
		<td>
			<?php if ($preuve_is_ok) :?>
				OK
			<?php else : ?>
				<?php echo  $preuve_error ?>
			<?php endif;?>
		</td>
</tr>
</table>
</div>
