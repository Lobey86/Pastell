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
					<input class='document_checkbox' type='checkbox' name='id_d[]' value='<?php echo $document['id_d']?>'/>
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
			<span class='action_submit' id='btn_<?php echo $action_name?>'>
			<button type='submit'  class='btn ' name='action' value='<?php echo $action_name?>'><?php hecho($theAction->getDoActionName($action_name)) ?></button>
			&nbsp;&nbsp;
			</span>
		<?php endforeach;?>
		<br/>
		<div class='alert alert_info' id='btn_message'></div>
	</form>
</div>
<script>

var all_tab = {
	<?php foreach($listDocument as $i => $document ) : ?>
		'<?php echo $document['id_d']?>': [
			<?php foreach($document['action_possible'] as $action_name) : ?>
			'<?php echo $action_name ?>',
			<?php endforeach;?>
		
		],	
	<?php endforeach;?>
};

function array_intersection(array1,array2){
	return array1.filter(function(n) {
	    return array2.indexOf(n) != -1
	});

}

$(document).ready(function(){

	$(".action_submit").hide();
	$("#btn_message").html("Veuillez sélectionner un ou plusieurs documents");
	
	$(".document_checkbox").click(function(){

		var checkedValues = $('.document_checkbox:checked').map(function() {
		    return this.value;
		}).get();
		var tab_result = [];
		for(i=0; i<checkedValues.length; i++){
			var id_d = checkedValues[i];
			var tab_tmp = all_tab[id_d];
			if (i == 0){
				tab_result = tab_tmp;
			} else {
				tab_result = array_intersection(tab_result,tab_tmp);
			}
			
		}
		
		$(".action_submit").each(function(){
			var action_id = this.id.substr(4);
			if ( tab_result.indexOf(action_id) == -1){
				$(this).hide();
			} else {
				$(this).show();
				$("#btn_message").hide();
			}
		});

		if (tab_result.length==0){
			$("#btn_message").show();
			if (checkedValues.length > 0){
				$("#btn_message").html("Aucune action n'est commune aux documents sélectionnés");
			} else {
				$("#btn_message").html("Veuillez sélectionner un ou plusieurs documents");
			}
		}
		
		
	});
});


</script>
