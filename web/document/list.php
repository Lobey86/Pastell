<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once (PASTELL_PATH . "/lib/document/DocumentEntite.class.php");
require_once (PASTELL_PATH . "/include/navigation_collectivite.php");


$recuperateur = new Recuperateur($_GET);
$type = $recuperateur->get('type');
$id_e = $recuperateur->get('id_e',0);

$entite = new Entite($sqlQuery,$id_e);
$infoEntite = $entite->getInfo();

if (! $roleUtilisateur->hasOneDroit($authentification->getId(),$type.":lecture")){
	header("Location: ".SITE_BASE . "index.php");
	exit;
}

$documentEntite = new DocumentEntite($sqlQuery);

$page_title = "Liste des documents $type";
if ($id_e){
	$page_title .= " pour " . $infoEntite['denomination'];
}

if ($roleUtilisateur->hasOneDroit($authentification->getId(),$type.":edition") && $id_e != 0){
	$nouveau_bouton_url = "document/edition.php?type=$type&id_e=$id_e";
}


include( PASTELL_PATH ."/include/haut.php");
?>

<?php if ($id_e != 0) : ?>
	<div class="box_contenu clearfix">
	
		<h2>Documents <?php echo $type ?> </h2>
			<table class="tab_01">
			<tr>
				<th>Objet</th>
				<th>Date</th>
				<?php if ( ! $type ) : ?>
					<th>Type</th>
				<?php endif; ?>
				<?php if ( ! $id_e) : ?>
					<th>Entité</th>
				<?php endif; ?>
			</tr>
		
		<?php $i = 0;
		
		foreach($documentEntite->getDocument($id_e , $type ) as $document ) : ?>
			<tr class='<?php echo ($i++)%2?'bg_class_gris':'bg_class_blanc'?>'>
			
				<td>
					<a href='document/detail.php?id_d=<?php echo $document['id_d']?>&id_e=<?php echo $id_e?>'>
						<?php echo $document['titre']?$document['titre']:$document['id_d']?>
					</a>			
				</td>
				<td>
					<?php echo $document['modification']?>
				</td>
				<?php if ( ! $type) : ?>
					<td>
						<?php echo FluxFactory::getTitreS($document['type']) ?>
					</td>
				<?php endif;?>
				
			</tr>
		<?php endforeach;?>
		</table>
						
		
	</div>
<?php endif;

navigation_collectivite($entite,"document/list.php?type=$type");

if ($id_e) : ?>
<a href='journal/index.php?id_e=<?php echo $id_e?>&type=<?php echo $type?>'>Voir le journal des évènements</a>
<br/><br/>
<?php 
endif;
include( PASTELL_PATH ."/include/bas.php");
