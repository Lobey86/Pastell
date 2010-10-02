<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once (PASTELL_PATH . "/lib/document/DocumentEntite.class.php");
require_once (PASTELL_PATH . "/include/navigation_collectivite.php");

require_once (PASTELL_PATH . "/lib/action/DocumentAction.class.php");
require_once (PASTELL_PATH . "/lib/action/ActionPossible.class.php");
require_once (PASTELL_PATH . "/lib/action/DocumentActionEntite.class.php");

$recuperateur = new Recuperateur($_GET);
$type = $recuperateur->get('type');
$id_e = $recuperateur->get('id_e',0);

$entite = new Entite($sqlQuery,$id_e);
$infoEntite = $entite->getInfo();



$liste_collectivite = array();

if ($id_e == 0){
	$liste_collectivite = $roleUtilisateur->getEntite($authentification->getId(),$type.":lecture");

	if ( ! $liste_collectivite){
		header("Location: ". SITE_BASE . "/index.php");
		exit;
	}

	if (count($liste_collectivite) == 1){
		$id_e = $liste_collectivite[0];
	}
	
} else if  (! $roleUtilisateur->hasDroit($authentification->getId(),$type.":lecture",$id_e)){
	header("Location: ".SITE_BASE . "index.php");
	exit;
}



$documentEntite = new DocumentEntite($sqlQuery);

$page_title = "Liste des documents $type";
if ($id_e){
	$page_title .= " pour " . $infoEntite['denomination'];
}

$documentAction = new DocumentAction($sqlQuery,$journal,0,$id_e,$authentification->getId());
//$actionPossible = new ActionPossible($sqlQuery,$documentAction);

if ($roleUtilisateur->hasDroit($authentification->getId(),$type.":edition",$id_e) && $id_e != 0){
	$nouveau_bouton_url = "document/edition.php?type=$type&id_e=$id_e";
}


include( PASTELL_PATH ."/include/haut.php");
?>

<?php if ($id_e != 0) : 
		$documentActionEntite = new DocumentActionEntite($sqlQuery);

$listDocument = $documentActionEntite->getListDocument($id_e , $type ) ;

$tabEntete = array();

foreach($listDocument as $doc){
	foreach($doc['action'] as $action => $date){
		if (! in_array($action,$tabEntete)){
			$tabEntete[] = $action;
		}
	}
}

?>
	<div class="box_contenu clearfix">
	
		<h2>Documents <?php echo $type ?> </h2>
			<table class="tab_01">
			<tr>
				<th>Objet</th>
				<th>Date</th>
				<?php foreach($tabEntete as $entete) : ?>
					<th><?php echo $entete?></th>
				<?php endforeach;?>
			</tr>
		
		<?php $i = 0;
		
		
		
		foreach($listDocument as $document ) : ?>
			<tr class='<?php echo ($i++)%2?'bg_class_gris':'bg_class_blanc'?>'>
			
				<td>
					<a href='document/detail.php?id_d=<?php echo $document['id_d']?>&id_e=<?php echo $id_e?>'>
						<?php echo $document['titre']?$document['titre']:$document['id_d']?>
					</a>			
				</td>
				<td>
					<?php echo $document['modification']?>
				</td>
				<?php foreach($tabEntete as $entete) : ?>
					<td>
						<?php if (isset($document['action'][$entete])) : ?>
							<?php echo $document['action'][$entete]?>
						<?php else : ?>
							&nbsp;
						<?php endif;?>
					</td>
				<?php endforeach;?>
			</tr>
		<?php endforeach;?>
		</table>
						
		
	</div>
<?php endif;


if (!$id_e && ! $roleUtilisateur->hasDroit($authentification->getId(),"journal:lecture",$id_e) ){
	navigation_racine($liste_collectivite,"document/list.php?type=$type");
} else {
	navigation_collectivite($entite,"document/list.php?type=$type");
}
if ($id_e) : ?>
<a href='journal/index.php?id_e=<?php echo $id_e?>&type=<?php echo $type?>'>Voir le journal des évènements</a>
<br/><br/>
<?php 
endif;
include( PASTELL_PATH ."/include/bas.php");
