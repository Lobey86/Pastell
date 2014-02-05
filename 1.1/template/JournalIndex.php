<?php if ($id_d) : ?>
<a href='journal/index.php?id_e=<?php echo $id_e?>'>« Journal de <?php echo $infoEntite['denomination']?></a>
<?php endif;?>
<?php if ($roleUtilisateur->hasDroit($authentification->getId(),"journal:lecture",$id_e)) : 
$this->SuivantPrecedent($offset,$limit,$count,"journal/index.php?id_e=$id_e&id_u=$id_u&recherche=$recherche&type=$type&id_d=$id_d");

?>
<div class="box_contenu clearfix">

<h2>Journal des évènements (extraits)</h2>

<form action="journal/index.php" method='get'>
	<input type='hidden' name='id_e' value='<?php echo $id_e?>'/>
	<input type='hidden' name='type' value='<?php echo $type?>'/>
	<input type='hidden' name='id_d' value='<?php echo $id_d?>'/>
	<input type='hidden' name='id_u' value='<?php echo $id_u?>'/>
	<input type='text' name='recherche' value='<?php echo $recherche ?>'/>
	<input type='submit' value='Chercher'/>
</form>

<table class="tab_01">
	<tr>
		<th>Numéro</th>
		<th>Date</th>
		<th>Type</th>
		<th>Entité</th>
		<th>Utilisateur</th>
		<th>Document</th>
		<th>État</th>
		<th>Message</th>
		<th>Horodatage</th>
	</tr>
<?php foreach($all as $i => $ligne) : ?>
	<tr  class='<?php echo $i%2?'bg_class_gris':'bg_class_blanc'?>'>
		<td><a href='journal/detail.php?id_j=<?php echo $ligne['id_j'] ?>&id_d=<?php echo $id_d?>&type=<?php echo $type ?>&id_e=<?php echo $id_e ?>&offset=<?php echo $offset?>'><?php echo $ligne['id_j']?></a></td>
		<td><?php echo  time_iso_to_fr($ligne['date']) ?></td>
		<td><?php echo $this->Journal->getTypeAsString($ligne['type']) ?></td>
		<td><a href='entite/detail.php?id_e=<?php echo $ligne['id_e'] ?>'><?php echo  $ligne['denomination']?></a></td>
		<td><a href='utilisateur/detail.php?id_u=<?php echo  $ligne['id_u']?>'><?php echo $ligne['prenom'] . " " . $ligne['nom']?></a></td>
		<td>
			<a href='document/detail.php?id_d=<?php echo $ligne['id_d']?>&id_e=<?php echo $ligne['id_e']?>'>
				<?php echo $ligne['titre']?:$ligne['id_d']?>
			</a>
		</td>
		<td>
		<?php
			if ($ligne['action'] == 'supression'){
				echo "Supprimé";
			} elseif ($ligne['id_d'] && $ligne['document_type']){ 
				$documentType = $this->DocumentTypeFactory->getFluxDocumentType($ligne['document_type']);
				$theAction = $documentType->getAction();
				echo $theAction->getActionName($ligne['action']);
			} else {
				echo  $ligne['action'];
			}
		?>
		</td>
		
		<td><?php echo $ligne['message']?></td>
		<td><?php if ($ligne['preuve']) : ?> 
			<?php echo time_iso_to_fr($ligne['date_horodatage']) ?>
			<?php else : ?>
			en cours
			<?php endif;?>
		</td>
	</tr>
<?php endforeach;?>
</table>
</div>

<a href='journal/export.php?format=csv&offset=0&limit=<?php echo $count ?>&id_e=<?php echo $id_e?>&type=<?php echo $type?>&id_d=<?php echo $id_d?>&id_u=<?php echo $id_u ?>&recherche=<?php echo $recherche ?>'>Récupérer le journal (CSV)</a>
<br/><br/>
<?php endif;?>
<?php 
$this->render("EntiteNavigation");


