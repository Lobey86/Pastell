<?php
require_once( dirname(__FILE__) . "/../init-authenticated.php");

$recuperateur = new Recuperateur($_GET);
$id_e = $recuperateur->getInt('id_e');
$flux = $recuperateur->get('flux');
$type = $recuperateur->get('type');

$droit_ecriture = $roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$id_e);

if ( ! $droit_ecriture ){
	header("Location: index.php");
	exit;
}

$entite = new Entite($sqlQuery,$id_e);
if ($id_e && ! $entite->exists()){
	header("Location: index.php");
	exit;
}
$info = $entite->getInfo();

$all_connecteur_dispo = $objectInstancier->ConnecteurFactory->getAllDispoEntite($id_e,$type);

if (! $all_connecteur_dispo){
	$lastError->setLastError("Aucun connecteur « $type » disponible !");
	header("Location: ".SITE_BASE."/entite/detail.php?id_e=$id_e&page=3");
	exit;
}

$page_title = "Association d'un connecteur et d'un flux";


include( PASTELL_PATH ."/include/haut.php");
?>
<?php include(PASTELL_PATH . "/include/bloc_message.php");?>

<a href='entite/detail.php?id_e=<?php echo $id_e ?>&page=3'>« Revenir</a>
<br/><br/>

<div class="box_contenu clearfix">

<h2>Associer un connecteur</h2>
<form class="w700" action='connecteur/flux-controler.php' method='post' >
<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />
<input type='hidden' name='flux' value='<?php echo $flux ?>' />
<input type='hidden' name='type' value='<?php echo $type ?>' />

<table >

<tr>
<th>Flux</th>
<td><?php hecho($objectInstancier->DocumentTypeFactory->getDocumentType($flux)->getName() );?></td>
</tr>
<tr>
<th>Type de connecteur nécessaire</th>
<td><?php hecho($type )?></td>
</tr>
<tr>
<th>Connecteur</th>
<td><select name='id_connecteur'>
		<?php foreach($all_connecteur_dispo as $id_connecteur => $connecteur) : ?>
			<option value='<?php hecho($id_connecteur)?>'><?php hecho($id_connecteur)?> (<?php hecho($connecteur['name'])?>)</option>
		<?php endforeach;?>
	</select></td>
</tr>

</table>
<input type='submit' value='Associer' />
</form>
</div>
<br/><br/>
<?php 
include( PASTELL_PATH ."/include/bas.php");