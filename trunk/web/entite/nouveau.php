<?php
require_once( dirname(__FILE__) . "/../init-admin.php");
require_once( ZEN_PATH . "/lib/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/entite/EntiteListe.class.php");


$recuperateur = new Recuperateur($_GET);
$type = $recuperateur->get('type');
$entite_mere = $recuperateur->get('entite_mere');
$siren = $recuperateur->get('siren');

$infoEntite = array('type' => '',
					'denomination' =>  $lastError->getLastInput('nom'),
					'siren' =>  $lastError->getLastInput('siren'),'entite_mere'=>'');

if ($siren){
	$entite = new Entite($sqlQuery,$siren);
	$infoEntite = $entite->getInfo();
	$page_title = "Modification de " . $infoEntite['denomination'];
} elseif ($entite_mere){
	$entite = new Entite($sqlQuery,$entite_mere);
	$infoMere = $entite->getInfo();
	$page_title = "Nouvelle fille pour " . $infoMere['denomination'];
} elseif($type){
	$page_title = "Nouveau [".Entite::getNom($type)."]";
} else {
	header("Location : index.php");
	exit;
}

$entiteListe = new EntiteListe($sqlQuery);
$mereListe = $entiteListe->getAllPossibleMother();


include( PASTELL_PATH ."/include/haut.php");
?>
<?php if ($siren) : ?>
	<a href='entite/detail.php?siren=<?php echo $siren?>'>
		« revenir à <?php echo $infoEntite['denomination']?>
	</a><br/><br/>
<?php endif;?>


<?php if ($entite_mere) : ?>
	<a href='entite/detail.php?siren=<?php echo $infoMere['siren']?>'>
		« revenir à <?php echo $infoMere['denomination']?>
	</a><br/><br/>
<?php endif;?>


<?php include (PASTELL_PATH."/include/bloc_message.php"); ?>


<div class="box_contenu clearfix">

<form class="w700" action="entite/nouveau-controler.php" method='post'>

<table>
<?php if ($type) : ?>
<input type='hidden' name='type' value='<?php echo $type ?>' />
<?php else : ?>
	<tr>
	<th>Type d'entité</th>
	<td><select name='type'>
	<?php foreach (array(Entite::TYPE_SERVICE,Entite::TYPE_COLLECTIVITE, Entite::TYPE_CENTRE_DE_GESTION) as $type) :?>
		<option value=<?php echo $type?>
			 <?php echo $infoEntite['type'] == $type?'selected="selected"':''?>> 
		<?php echo $type ?> </option>	
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
<?php if ($siren) : ?>
<?php echo $siren?>
	<input type='hidden' name='siren' value='<?php echo $siren ?>' />

<?php else :?>
<input type="text" name="siren" id="siren" value='<?php echo $infoEntite['siren']?>'/></td>
<?php endif;?>
</tr>
<?php if ($entite_mere) : ?>
	<input type='hidden' name='mere_siren' value='<?php echo $entite_mere ?>' />
<?php else : ?>
	<tr>
		<th><label for="mere_siren">Entité mère</label></th>
		<td>
			<select name='mere_siren'>
				<option value=''>---</option>
				<?php foreach($mereListe as $mere) : ?>
					<option value='<?php echo $mere['siren']?>' <?php echo $infoEntite['entite_mere'] == $mere['siren']?"selected='selected'":''?>>
						<?php echo $mere['denomination']?>
					</option>
				<?php endforeach;?>
			</select>
		</td>
	</tr>
<?php endif;?>
</table>
<span>*</span> champs obligatoires

<div class="align_right">
<?php if ($siren) : ?>
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

