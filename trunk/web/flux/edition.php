<?php
require_once( dirname(__FILE__) . "/../init-authenticated.php");

$recuperateur = new Recuperateur($_GET);
$id_e = $recuperateur->getInt('id_e');
$flux = $recuperateur->get('flux');
$type_connecteur = $recuperateur->get('type');

$connecteur_disponible = $objectInstancier->FluxControler->getConnecteurDispo($id_e,$type_connecteur);

$page_title = "Association d'un connecteur et d'un flux";


include( PASTELL_PATH ."/include/haut.php");
?>
<?php include(PASTELL_PATH . "/include/bloc_message.php");?>

<a href='entite/detail.php?id_e=<?php echo $id_e ?>&page=3'>« Revenir</a>
<br/><br/>

<div class="box_contenu clearfix">

<h2>Associer un connecteur</h2>
<form class="w700" action='flux/edition-controler.php' method='post' >
<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />
<input type='hidden' name='flux' value='<?php echo $flux ?>' />
<input type='hidden' name='type' value='<?php echo $type_connecteur ?>' />

<table >

<tr>
<th>Flux</th>
<td><?php hecho($objectInstancier->DocumentTypeFactory->getDocumentType($flux)->getName() );?></td>
</tr>
<tr>
<th>Type de connecteur nécessaire</th>
<td><?php hecho($type_connecteur )?></td>
</tr>
<tr>
<th>Connecteur</th>
<td><select name='id_ce'>
		<?php foreach($connecteur_disponible as $connecteur) : ?>
			<option value='<?php hecho($connecteur['id_ce'])?>'><?php hecho($connecteur['id_connecteur'])?> (<?php hecho($connecteur['libelle'])?>)</option>
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