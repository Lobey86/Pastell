<?php
require_once( dirname(__FILE__) . "/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/entite/EntiteListeHTML.class.php");

$recuperateur = new Recuperateur($_GET);

$entite_mere = $recuperateur->get('entite_mere');
$id_e = $recuperateur->getInt('id_e',0);


if ( (! $roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$id_e))
	&& (! $roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$entite_mere)))
	{
	header("Location: " . SITE_BASE ."index.php");
	exit;
}


$infoEntite = array('type' =>  $lastError->getLastInput('type'),
					'denomination' =>  $lastError->getLastInput('nom'),
					'siren' =>  $lastError->getLastInput('siren'),
					'entite_mere'=> $lastError->getLastInput('entite_mere'),
					'id_e' => $lastError->getLastInput('id_e'),
					'has_ged' =>  $lastError->getLastInput('has_ged'),
					'has_archivage' =>  $lastError->getLastInput('has_archivage'),
					'centre_de_gestion' => $lastError->getLastInput('centre_de_gestion'),
);

if ($id_e){
	$entite = new Entite($sqlQuery,$id_e);
	$infoEntite = $entite->getInfo();
	$infoEntite['centre_de_gestion'] = $entite->getCDG(); 
	$page_title = "Modification de " . $infoEntite['denomination'];
	$entiteProperties = new EntitePropertiesSQL($sqlQuery);
	
	$infoEntite['has_ged'] = $entiteProperties->getProperties($id_e,EntitePropertiesSQL::ALL_FLUX,'has_ged');
	$infoEntite['has_archivage'] = $entiteProperties->getProperties($id_e,EntitePropertiesSQL::ALL_FLUX,'has_archivage');
	
} elseif ($entite_mere){
	$entite = new Entite($sqlQuery,$entite_mere);
	$infoMere = $entite->getInfo();
	$page_title = "Nouvelle fille pour " . $infoMere['denomination'];
} else {
	$page_title = "Création d'une collectivité";
} 
	

$entiteListe = new EntiteListe($sqlQuery);

$allCDG = $entiteListe->getAll(Entite::TYPE_CENTRE_DE_GESTION);

$mereListe = $entiteListe->getAllPossibleMother();

$entiteListeHTML = new EntiteListeHTML();

include( PASTELL_PATH ."/include/haut.php");
?>
<?php if ($id_e) : ?>
	<a href='entite/detail.php?id_e=<?php echo $id_e?>'>
		« revenir à <?php echo $infoEntite['denomination']?>
	</a>
<?php elseif ($entite_mere) : ?>
	<a href='entite/detail.php?id_e=<?php echo $infoMere['id_e']?>'>
		« revenir à <?php echo $infoMere['denomination']?>
	</a>
<?php else: ?>
	<a href='entite/detail0.php'>
		« revenir à la liste des collectivités
	</a>
<?php endif;?>
<br/><br/>

<?php include (PASTELL_PATH."/include/bloc_message.php"); ?>

<div class="box_contenu clearfix">

<form class="w700" action="entite/edition-controler.php" method='post'>
<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />

<?php if ($entite_mere) : ?>
<input type='hidden' name='entite_mere' value='<?php echo $entite_mere ?>' />
<?php else: ?>
<input type='hidden' name='entite_mere' value='<?php echo $infoEntite['entite_mere'] ?>' />

<?php endif;?>

<table>
	<tr>
	<th>Type d'entité</th>
	<td><select name='type'>
	<?php foreach (array(Entite::TYPE_COLLECTIVITE, Entite::TYPE_CENTRE_DE_GESTION,Entite::TYPE_SERVICE) as $type) :?>
		<option value='<?php echo $type?>'
			 <?php echo $infoEntite['type'] == $type?'selected="selected"':''?>> 
		<?php echo Entite::getNom($type) ?> </option>	
		<?php endforeach;?>
		
	</select></td>
	</tr>
<tr>
<th><label for="nom">Nom<span>*</span></label>
<p class='form_commentaire'>60 caractères max</p>
</th>

<td><input type="text" name="nom" id="nom" value='<?php echo $infoEntite['denomination']?>'/></td>
</tr>
<tr>
<th><label for="siren">SIREN<span>*</span></label>
<p class='form_commentaire'>9 caractères obligatoires </p>
<p class='form_commentaire'>obligatoire pour une collectivité</p></th>
<td>
	<input type="text" name="siren" id="siren" value='<?php echo $infoEntite['siren']?>'/></td>

</tr>

<tr>
	<th><label for="cdg">Centre de gestion</label></th>
	<td>
		<?php $entiteListeHTML->getCDGasSelect($allCDG,$infoEntite['centre_de_gestion'])?>
	</td>
</tr>

<tr>
<th><label for="has_ged">Utilisation d'une GED</label></th>
<td>
	<select name='has_ged'>
	<?php 

		$option = array('non'=>'non','manuel' => 'oui, manuellement' , 'auto' => 'oui, automatiquement');
		foreach($option as $key => $values): ?>
			<option value='<?php echo $key ?>' <?php echo $infoEntite['has_ged'] == $key?'selected="selected"':''?>><?php echo $values?></option>
	<?php endforeach; ?>

	</select>
</tr>
<tr>
<th><label for="has_archivage">Utilisation d'un SAE</label></th>
<td>
	<select name='has_archivage'>
		<?php 
		foreach($option as $key => $values): ?>
			<option value='<?php echo $key ?>' <?php echo $infoEntite['has_archivage'] == $key?'selected="selected"':''?>><?php echo $values?></option>
	<?php endforeach; ?>
	</select>
</tr>
</table>
<span>*</span> champs obligatoires 

<div class="align_right">
<?php if ($id_e) : ?>
<input type="submit" value="Modifier" class="submit" />

<?php else : ?>
<input type="submit" value="Créer" class="submit" />
<?php endif;?>
</div>


</form>

</div>

<?php 
include( PASTELL_PATH . "/include/demo-box-siren.php");
include( PASTELL_PATH ."/include/bas.php");

