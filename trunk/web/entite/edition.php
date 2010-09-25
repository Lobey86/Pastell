<?php
require_once( dirname(__FILE__) . "/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/entite/EntiteListe.class.php");


$recuperateur = new Recuperateur($_GET);

$entite_mere = $recuperateur->get('entite_mere');
$id_e = $recuperateur->getInt('id_e',0);


if ( (! $roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$id_e))
	&& (! $roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$entite_mere)))
	{
	header("Location: " . SITE_BASE ."index.php");
	exit;
}


$infoEntite = array('type' => '',
					'denomination' =>  $lastError->getLastInput('nom'),
					'siren' =>  $lastError->getLastInput('siren'),
					'entite_mere'=>'',
					'id_e' => $lastError->getLastInput('id_e'),
);

if ($id_e){
	$entite = new Entite($sqlQuery,$id_e);
	$infoEntite = $entite->getInfo();
	$page_title = "Modification de " . $infoEntite['denomination'];
} elseif ($entite_mere){
	$entite = new Entite($sqlQuery,$entite_mere);
	$infoMere = $entite->getInfo();
	$page_title = "Nouvelle fille pour " . $infoMere['denomination'];
} else {
	$page_title = "Création d'une collectivité";
} 


$entiteListe = new EntiteListe($sqlQuery);

$mereListe = $entiteListe->getAllPossibleMother();

include( PASTELL_PATH ."/include/haut.php");
?>
<?php if ($id_e) : ?>
	<a href='entite/detail.php?id_e=<?php echo $id_e?>'>
		« revenir à <?php echo $infoEntite['denomination']?>
	</a><br/><br/>
<?php endif;?>


<?php if ($entite_mere) : ?>
	<a href='entite/detail.php?id_e=<?php echo $infoMere['id_e']?>'>
		« revenir à <?php echo $infoMere['denomination']?>
	</a><br/><br/>
<?php endif;?>


<?php include (PASTELL_PATH."/include/bloc_message.php"); ?>


<div class="box_contenu clearfix">

<form class="w700" action="entite/edition-controler.php" method='post'>
<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />

<?php if ($entite_mere) : ?>
<input type='hidden' name='type' value='<?php echo Entite::TYPE_SERVICE ?>' />
<input type='hidden' name='entite_mere' value='<?php echo $entite_mere ?>' />
<?php else: ?>
<input type='hidden' name='entite_mere' value='<?php echo $infoEntite['entite_mere'] ?>' />

<?php endif;?>
<table>
<?php if ( ! $entite_mere) : ?>
	<tr>
	<th>Type d'entité</th>
	<td><select name='type'>
	<?php foreach (array(Entite::TYPE_COLLECTIVITE, Entite::TYPE_CENTRE_DE_GESTION) as $type) :?>
		<option value=<?php echo $type?>
			 <?php echo $infoEntite['type'] == $type?'selected="selected"':''?>> 
		<?php echo Entite::getNom($type) ?> </option>	
		<?php endforeach;?>
	</select></td>
	</tr>
<?php endif;?>
<tr>
<th><label for="nom">Nom<span>*</span></label></th>

<td><input type="text" name="nom" id="nom" value='<?php echo $infoEntite['denomination']?>'/></td>
</tr>
<tr>
<th><label for="siren">Siren<span>*</span></label></th>
<td>
	<input type="text" name="siren" id="siren" value='<?php echo $infoEntite['siren']?>'/></td>

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

