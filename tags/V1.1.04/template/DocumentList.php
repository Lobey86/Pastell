<?php
if ($id_e != 0) {
	
?>
<div>
<form action='document/list.php' method='get' >
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
	<input type='submit' value='Rechercher' />
	
</form>

<p class='petit'><a href='document/search.php?id_e=<?php echo $id_e?>&type=<?php echo $type?>'>Recherche avanc�e</a></p>
</div>
<?php
	if ($last_id){
		$offset = $documentActionEntite->getOffset($last_id,$id_e,$type,$limit);
	}

	$count = $documentActionEntite->getNbDocument($id_e,$type,$search,$filtre);
	
	$this->SuivantPrecedent($offset,$limit,$count,"document/list.php?id_e=$id_e&type=$type&search=$search");
	
	$this->render("DocumentListBox");

}

$this->render("EntiteNavigation");


if ($id_e) : ?>
<a href='journal/index.php?id_e=<?php echo $id_e?>&type=<?php echo $type?>'>Voir le journal des �v�nements</a>
<br/><br/>
<?php 
endif;
