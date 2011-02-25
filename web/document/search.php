<?php

require_once(dirname(__FILE__)."/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once (PASTELL_PATH . "/lib/action/DocumentActionEntite.class.php");
require_once( PASTELL_PATH . "/lib/helper/suivantPrecedent.php");
require_once( PASTELL_PATH . "/lib/document/DocumentListAfficheur.class.php");
require_once (PASTELL_PATH . "/lib/entite/NavigationEntite.class.php");
require_once( PASTELL_PATH . "/lib/document/DocumentTypeHTML.class.php");

$documentTypeHTML = new DocumentTypeHTML();

$recuperateur = new Recuperateur($_GET);
$id_e = $recuperateur->get('id_e',0);
$type = $recuperateur->get('type');
$go = $recuperateur->get('go',0);


$offset = $recuperateur->getInt('offset',0);


$search = $recuperateur->get('search');

$limit = 20;


$documentActionEntite = new DocumentActionEntite($sqlQuery);

$liste_type = array();
$allDroit = $roleUtilisateur->getAllDroit($authentification->getId());

$arbre = $roleUtilisateur->getArbreFille($authentification->getId(),"entite:lecture");

$page_title= "Recherche avancée de document";
include( PASTELL_PATH ."/include/haut.php");

$listeEtat = $documentTypeFactory->getAllAction();

?>

<div class="box_contenu clearfix">

<form class="w700" action='document/search.php' method='get' >
<input type='hidden' name='go' value='go' />
					
<table>
	<tr>
	<th>Type de document</th>
	<td><?php  $documentTypeHTML->displaySelect($documentTypeFactory); ?></td>
	</tr>
	<tr>
	<th>Collectivité</th>
	<td>	
		<select name='id_e'>
			<option value=''>Toutes les collectivités</option>
			<?php foreach($arbre as $entiteInfo): ?>
			<option value='<?php echo $entiteInfo['id_e']?>'>
				<?php for($i=0; $i<$entiteInfo['profondeur']; $i++){ echo "&nbsp&nbsp;";}?>
				|_<?php echo $entiteInfo['denomination']?> </option>
			<?php endforeach ; ?>
		</select>
		</td>
	</tr>
	<tr>
		<th>Dernier état</th>
		<td><select name='etat'>
			<option value=''>N'importe quel état</option>
			<?php foreach($listeEtat as $etat => $nameEtat): ?>
				<option value='<?php echo $etat?>'><?php echo $nameEtat?></option>
			<?php endforeach ; ?>
		</select></td>
	</tr>
	<tr>
		<th>Date de passage dans le dernier état</th>
		<td>Du: 	<?php dateInput('last_state_begin'); ?> <br/>
			Au : <?php dateInput('last_state_end'); ?> 
		</td>
	</tr>
	<tr>
		<th>Passé par l'état</th>
		<td><select name='etat'>
			<option value=''>----</option>
			<?php foreach($listeEtat as $etat => $nameEtat): ?>
				<option value='<?php echo $etat?>'><?php echo $nameEtat?></option>
			<?php endforeach ; ?>
		</select></td>
	</tr>
	<tr>
		<th>Date de passage dans cet état</th>
		<td>Du: 	<?php dateInput('state_begin'); ?> <br/>
			Au : <?php dateInput('state_end'); ?> 
		</td>
	</tr>
	<tr>
		<th>Dont le titre contient</th>
		<td><input type='text' name='search' value='<?php echo $search?>'/></td>
	</tr>
</table>
	
	<input type='submit' value='Rechercher' />
</form>
</div>
<?php 

if ($go = 'go'){
	
	$listDocument = $documentActionEntite->getListBySearch($id_e,$type,$offset,$limit,$search);	
	$count = $documentActionEntite->getNbDocumentBySearch($id_e,$type,$search);
	
	suivant_precedent($offset,$limit,$count,"document/search.php?id_e=$id_e&search=$search");
	$documentListAfficheur = new DocumentListAfficheur($documentTypeFactory);
	
	$documentListAfficheur->affiche($listDocument,$id_e);

}



include( PASTELL_PATH ."/include/bas.php");
