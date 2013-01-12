<?php
require_once( dirname(__FILE__) . "/../init-authenticated.php");

$recuperateur = new Recuperateur($_GET);
$id_ce = $recuperateur->getInt('id_ce');

$objectInstancier->ConnecteurControler->verifDroitOnConnecteur($id_ce);

$connecteur_entite_info = $objectInstancier->ConnecteurEntiteSQL->getInfo($id_ce);

$page_title = "Modification du connecteur  « {$connecteur_entite_info['libelle']} »";

include( PASTELL_PATH ."/include/haut.php");
?>
<?php include(PASTELL_PATH . "/include/bloc_message.php");?>



<a href='connecteur/edition.php?id_ce=<?php echo $connecteur_entite_info['id_ce'] ?>'>« Revenir à la définition du connecteur</a>
<br/><br/>
<div class="box_contenu clearfix">
<h2>Connecteur <?php hecho($connecteur_entite_info['type']) ?> - <?php hecho($connecteur_entite_info['id_connecteur'])?> : <?php hecho($connecteur_entite_info['libelle']) ?> 
</h2>
<br/><br/>

<form class="w700" action='connecteur/edition-libelle-controler.php' method='post' >
	<input type='hidden' name='id_ce' value='<?php echo $connecteur_entite_info['id_ce'] ?>' />
<table >

<tr>
<th>Libellé</th>
<td><input type='text' name='libelle' value='<?php hecho($connecteur_entite_info['libelle']) ?>'/></td>
</tr>

</table>
	
	<input type='submit' value='Modifier le libellé'/>
</form>

</div>

<?php 
include( PASTELL_PATH ."/include/bas.php");
