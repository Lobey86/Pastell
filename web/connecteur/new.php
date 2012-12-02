<?php
require_once( dirname(__FILE__) . "/../init-authenticated.php");

$recuperateur = new Recuperateur($_GET);
$id_e = $recuperateur->getInt('id_e');

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

$all_connecteur_dispo = $objectInstancier->ConnecteurDefinitionFiles->getAllByIdE($id_e);


$page_title = "Ajout d'un connecteur";


include( PASTELL_PATH ."/include/haut.php");
?>
<?php include(PASTELL_PATH . "/include/bloc_message.php");?>

<a href='entite/detail.php?id_e=<?php echo $id_e ?>&page=2'>« Retour</a>
<br/><br/>

<div class="box_contenu clearfix">

<h2>Ajouter un connecteur</h2>
<form class="w700" action='connecteur/new-controler.php' method='post' >
<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />
<table >

<tr>
<th>Libellé</th>
<td><input type='text' name='libelle' value=''/></td>
</tr>

<tr>
<th>Connecteur</th>
<td><select name='id_connecteur'>
		<?php foreach($all_connecteur_dispo as $id_connecteur => $connecteur) : ?>
			<option value='<?php hecho($id_connecteur)?>'><?php hecho($connecteur['name'])?> (<?php hecho($connecteur['type'])?>)</option>
		<?php endforeach;?>
	</select></td>
</tr>

</table>
<input type='submit' value='Créer un connecteur' />
</form>
</div>
<br/><br/>
<?php 
include( PASTELL_PATH ."/include/bas.php");