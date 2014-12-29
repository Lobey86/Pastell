<a class='btn btn-mini' href='document/traitement-lot.php?id_e=<?php echo $id_e ?>&type=<?php echo $type?>&search=<?php echo $search ?>&filtre=<?php echo $filtre?>&offset=<?php echo $offset ?>'><i class='icon-circle-arrow-left'></i>Retour</a>
<div class="box">
	<h2>Confirmez-vous l'action «<?php echo $theAction->getDoActionName($action_selected) ?>» sur ces documents ? </h2>
	<form action='document/do-traitement-par-lot.php' method='post'>
		<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />
		<input type='hidden' name='type' value='<?php echo $type ?>' />
		<input type='hidden' name='search' value='<?php echo $search ?>' />
		<input type='hidden' name='filtre' value='<?php echo $filtre ?>' />
		<input type='hidden' name='offset' value='<?php echo $offset ?>' />
		<input type='hidden' name='action' value='<?php echo $action_selected ?>' />
		<table class="table table-striped">
			<tr>
				<th class='w140'>Objet</th>
				<th>Dernier état</th>
				<th>Date</th>
			</tr>
			<?php foreach($listDocument as $i => $document ) : ?>
			
			<tr>
				<td>
				<input type='hidden' name='id_d[]' value='<?php echo $document['id_d']?>'/>
				<a href='document/detail.php?id_d=<?php echo $document['id_d']?>&id_e=<?php echo $document['id_e']?>'>
						<?php echo $document['titre']?$document['titre']:$document['id_d']?>
					</a>			
				</td>
				<td>
					<?php echo $theAction->getActionName($document['last_action_display']) ?>
				</td>
				<td>
					<?php echo time_iso_to_fr($document['last_action_date']) ?>
				</td>
				
			</tr>
		<?php endforeach;?>
		</table>
	
		<input type='submit' class='btn ' value='Confirmer «<?php hecho($theAction->getDoActionName($action_selected)) ?>»'/>&nbsp;&nbsp;
	</form>
</div>
