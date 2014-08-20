<?php
if ($id_e != 0) {
?>

<div class="box">

<form class="form-inline" action='document/list.php' method='get'>
	<input type='hidden' name='id_e' value='<?php echo $id_e?>'/>
	<input type='hidden' name='type' value='<?php echo $type?>'/>
	<input type='text' name='search' value='<?php echo $search?>'/>
	<select name='filtre'>
		<option value=''>...</option>
		<?php foreach($all_action as $etat => $libelle_etat) : ?>
			<option value='<?php echo $etat?>'
				<?php echo $filtre==$etat?"selected='selected'":""?>
			
			><?php echo $libelle_etat?></option>
		<?php endforeach;?>
	</select>
	<button type='submit' class='btn'><i class="icon-search"></i>Rechercher</button>
	<a style="margin-left:80px;" href='document/search.php?id_e=<?php echo $id_e?>&type=<?php echo $type?>'>Recherche avancée</a>
</form>

</div>


<?php
	if ($last_id){
		$offset = $documentActionEntite->getOffset($last_id,$id_e,$type,$limit);
	}
	
	$count = $documentActionEntite->getNbDocument($id_e,$type,$search,$filtre);
	
	$this->SuivantPrecedent($offset,$limit,$count,"document/list.php?id_e=$id_e&type=$type&search=$search&filtre=$filtre&tri=$tri&sens_tri=$sens_tri");

	$this->render("DocumentListBox");
	

}

if ($type && $id_e) :
?>
<div class="box">
	<h2>Traitement pas lot</h2>
	<form action='document/traitement-lot.php' method='get'>
		<input type='hidden' name='id_e' value='<?php echo $id_e?>'/>
		<input type='hidden' name='type' value='<?php echo $type?>'/>
		<input type='hidden' name='search' value='<?php echo $search?>'/>
		<input type='hidden' name='offset' value='<?php echo $offset?>'/>
		<input type='hidden' name='filtre' value='<?php echo $filtre?>'/>
		<input type='submit' value='Traitement par lot' class='btn'/>
	</form> 
</div>
<?php 
endif;

$this->render("EntiteNavigation");


if ($id_e) : ?>
<a class='btn btn-mini' href='journal/index.php?id_e=<?php echo $id_e?>&type=<?php echo $type?>'><i class='icon-list'></i>Voir le journal des évènements</a>
<?php 
endif;

