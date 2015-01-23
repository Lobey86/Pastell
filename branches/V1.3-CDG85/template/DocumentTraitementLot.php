<a class='btn btn-mini' href='document/list.php?id_e=<?php echo $id_e ?>&type=<?php echo $type?>&search=<?php echo $search ?>&filtre=<?php echo $filtre?>&offset=<?php echo $offset ?>'><i class='icon-circle-arrow-left'></i>Retour</a>
<div class="box">
	<h2>Documents <?php echo  	$this->DocumentTypeFactory->getFluxDocumentType($type)->getName() ?> </h2>
	
	<form action='document/confirm-traitement-par-lot.php' method='get'>
		<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />
		<input type='hidden' name='type' value='<?php echo $type ?>' />
		<input type='hidden' name='search' value='<?php echo $search ?>' />
		<input type='hidden' name='filtre' value='<?php echo $filtre ?>' />
		<input type='hidden' name='offset' value='<?php echo $offset ?>' />
		<table class="table table-striped">
			<tr>
				<th><input type="checkbox" name="select-all" id="select-all" /></th>
				<th class='w140'>Objet</th>
				<th>Dernier état</th>
				<th>Date</th>
				<th>États possibles</th>
			</tr>
			<?php foreach($listDocument as $i => $document ) : ?>
			<tr>
				<td>
					<input type='checkbox' name='id_d[]' value='<?php echo $document['id_d']?>'/>
				</td>
				<td>
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
				<td>
					<ul>
					<?php foreach($document['action_possible'] as $action_name) : ?>
						<li><?php hecho($theAction->getDoActionName($action_name)) ?></li>
					<?php endforeach;?>
					</ul>
				</td>
			</tr>
		<?php endforeach;?>
		</table>
		<?php foreach($all_action as $action_name):?>
			<button type='submit' class='btn' name='action' value='<?php echo $action_name?>'><?php hecho($theAction->getDoActionName($action_name)) ?></button>&nbsp;&nbsp;
		<?php endforeach;?>
	</form>
</div>
