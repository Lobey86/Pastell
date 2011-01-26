<?php
require_once( dirname(__FILE__) . "/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/entite/EntiteListe.class.php");
require_once( PASTELL_PATH . "/lib/entite/EntiteListeHTML.class.php");

$recuperateur = new Recuperateur($_GET);

$id_e = $recuperateur->getInt('id_e',0);

if ( ! $roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$id_e) ) {
	header("Location: " . SITE_BASE ."index.php");
	exit;
}

if ($id_e){
	$entite = new Entite($sqlQuery,$id_e);
	$info = $entite->getInfo();
}

$entiteListe = new EntiteListe($sqlQuery);

$allCDG = $entiteListe->getAll(Entite::TYPE_CENTRE_DE_GESTION);

$entiteListeHTML = new EntiteListeHTML();

$page_title = "Importer des collectivités";

include( PASTELL_PATH ."/include/haut.php");
include (PASTELL_PATH."/include/bloc_message.php"); 
?>
<div class="box_contenu clearfix">

<form class="w700" action="entite/import-controler.php" method='post' enctype='multipart/form-data'>
<input type='hidden' name='id_e' value='<?php echo $id_e?>' />
<table>
	<?php if ($id_e) : ?>
	<tr>
		<th>Collectivité parente</th>
		<td><?php echo $info['denomination']?></td>
	</tr>
	<?php endif;?>
	
	<tr>
		<th>Fichier CSV</th>
		<td><input type='file' name='csv_col'/></td>
	</tr>
	<tr>
		<th>Centre de gestion</th>
		<td><?php $entiteListeHTML->getCDGasSelect($allCDG,false)?></td>
	</tr>
</table>
<input type="submit" value="Importer" class="submit" />

</form>
</div>

<div class="box_info">
<p><strong>Format du fichier</strong></p>
<p>Le fichier CSV doit contenir une collectivité par ligne.</p>
<p>Les lignes sont formatés de la manière suivante : "libellé collectivité";"siren"</p>

</div>

<?php 
include( PASTELL_PATH ."/include/bas.php");

