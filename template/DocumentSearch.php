<?php 

function dateInput($name,$value=''){
	?>
	<input 	type='text' 	
		id='<?php echo $name?>' 
		name='<?php echo $name?>' 
		value='<?php echo $value?>' 
		class='date'
		/>
	<script type="text/javascript">
   		 jQuery.datepicker.setDefaults(jQuery.datepicker.regional['fr']);
		$(function() {
			$("#<?php echo $name?>").datepicker( { dateFormat: 'dd/mm/yy' });
			
		});
	</script>
	<?php 
}

?>
<div class="box">

<form action='document/search.php' method='get' >
<input type='hidden' name='go' value='go' />
					
<?php  $this->RechercheAvanceFormulaireHTML->display(); ?>

	<input type='submit' class='btn' value='Rechercher' />
</form>
</div>
<?php 

$url = "id_e=$id_e&search=$search&type=$type&lastetat=$lastEtat&last_state_begin=$last_state_begin_iso&last_state_end=$last_state_end_iso&etatTransit=$etatTransit&state_begin=$state_begin_iso&state_end=$state_end_iso&tri=$tri&sens_tri=$sens_tri";

if ($type){
	foreach($indexedFieldValue as $indexName => $indexValue){
		$url.="&".urlencode($indexName)."=".urlencode($indexValue);
	}
}

if ($go = 'go'){
	
	$listDocument = $documentActionEntite->getListBySearch($id_e,$type,$offset,$limit,$search,$lastEtat,$last_state_begin_iso,$last_state_end_iso,$tri,$allDroitEntite,$etatTransit,$state_begin_iso,$state_end_iso);	
	$count = $documentActionEntite->getNbDocumentBySearch($id_e,$type,$search,$lastEtat,$last_state_begin_iso,$last_state_end_iso,$allDroitEntite,$etatTransit,$state_begin_iso,$state_end_iso,$indexedFieldValue);
	if ($count) {
		$this->SuivantPrecedent($offset,$limit,$count,"document/search.php?$url");
		$this->render("DocumentListBox");

		
		?>
			<a class='btn btn-mini' href='document/search-export.php?<?php echo $url?>'><i class='icon-file'></i>Exporter les informations (CSV)</a>
		<?php 
	} else {
		?>
		<div class="alert alert-info">
			Les critères de recherches ne correspondent à aucun document
		</div>
		<?php 
	}
}

