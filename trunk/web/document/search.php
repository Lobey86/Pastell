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
$search = $recuperateur->get('search');

$lastEtat = $recuperateur->get('lastetat');
$last_state_begin = $recuperateur->get('last_state_begin');
$last_state_end = $recuperateur->get('last_state_end');

$last_state_begin_iso = getDateIso($last_state_begin);
$last_state_end_iso = getDateIso($last_state_end);

$etatTransit = $recuperateur->get('etatTransit');
$state_begin =  $recuperateur->get('state_begin');
$state_end =  $recuperateur->get('state_end');
$tri =  $recuperateur->get('tri');

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

$allDroit = $roleUtilisateur->getAllDroit($authentification->getId());
$listeEtat = $documentTypeFactory->getActionByRole($allDroit);

?>

<div class="box_contenu clearfix">

<form class="w700" action='document/search.php' method='get' >
<input type='hidden' name='go' value='go' />
					
<table>
	<tr>
	<th>Type de document</th>
	<td><?php  $documentTypeHTML->displaySelect($documentTypeFactory,$type); ?></td>
	</tr>
	<tr>
	<th>Collectivité</th>
	<td>	
		<select name='id_e'>
			<option value=''>Toutes les collectivités</option>
			<?php foreach($arbre as $entiteInfo): ?>
			<option value='<?php echo $entiteInfo['id_e']?>' <?php echo $entiteInfo['id_e'] == $id_e?"selected='selected'":"";?>>
				<?php for($i=0; $i<$entiteInfo['profondeur']; $i++){ echo "&nbsp&nbsp;";}?>
				|_<?php echo $entiteInfo['denomination']?> </option>
			<?php endforeach ; ?>
		</select>
		</td>
	</tr>
	<tr>
		<th>Dernier état</th>
		<td><select name='lastetat'>
			<option value=''>N'importe quel état</option>
			<?php foreach($listeEtat as $etat => $nameEtat): ?>
				<option value='<?php echo $etat?>' <?php echo $etat == $lastEtat?"selected='selected'":"";?>>
					<?php echo $nameEtat?>
				</option>
			<?php endforeach ; ?>
		</select></td>
	</tr>
	<tr>
		<th>Date de passage dans le dernier état</th>
		<td>Du: 	<?php dateInput('last_state_begin',$last_state_begin); ?> <br/>
			Au : <?php dateInput('last_state_end',$last_state_end); ?> 
		</td>
	</tr>
	<tr>
		<th>Passé par l'état</th>
		<td><select name='etatTransit'>
			<option value=''>----</option>
			<?php foreach($listeEtat as $etat => $nameEtat): ?>
				<option value='<?php echo $etat?>' <?php echo $etat == $etatTransit?"selected='selected'":"";?>>
					<?php echo $nameEtat?>
				</option>
			<?php endforeach ; ?>
		</select></td>
	</tr>
	<tr>
		<th>Date de passage dans cet état</th>
		<td>Du: 	<?php dateInput('state_begin',$state_begin); ?> <br/>
			Au : <?php dateInput('state_end',$state_end); ?> 
		</td>
	</tr>
	<tr>
		<th>Dont le titre contient</th>
		<td><input type='text' name='search' value='<?php echo $search?>'/></td>
	</tr>
	<tr>
		<th>Trier le résultat</th>
		<td>
			<select name='tri'>
				<?php 
					foreach(array('last_action_date' => "Date de dernière modification",
									"title" => 'Titre du document',
									"entite" => "Nom de l'entité",							)
						as $key => $libelle
					) :
				?>
				<option value='<?php echo $key?>' <?php echo $tri==$key?'selected="selected"':''?>><?php echo $libelle?></option>
				<?php endforeach;?>
			</select>
		</td>
	</tr>
</table>
	
	<input type='submit' value='Rechercher' />
</form>
</div>
<?php 

$url = "id_e=$id_e&search=$search&type=$type&lastetat=$lastEtat&last_state_begin=$last_state_begin&last_state_end=$last_state_end&etatTransit=$etatTransit&state_begin=$state_begin&state_end=$state_end&tri=$tri";
if ($go = 'go'){
	
	$listDocument = $documentActionEntite->getListBySearch($id_e,$type,$offset,$limit,$search,$lastEtat,$last_state_begin_iso,$last_state_end_iso,$tri);	
	$count = $documentActionEntite->getNbDocumentBySearch($id_e,$type,$search,$lastEtat,$last_state_begin_iso,$last_state_end_iso);
	if ($count) {
		suivant_precedent($offset,$limit,$count,"document/search.php?$url");
		$documentListAfficheur = new DocumentListAfficheur($documentTypeFactory);
		$documentListAfficheur->affiche($listDocument,$id_e);
		?>
			<a href='document/search-export.php?<?php echo $url?>'>Exporter les informations (CSV)</a>
			<br/><br/>
		<?php 
	} else {
		?>
		<div class="box_info">
			<p>Les critères de recherches ne correspondent à aucun document</p>
		</div>
		<?php 
	}
}


include( PASTELL_PATH ."/include/bas.php");
