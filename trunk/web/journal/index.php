<?php

require_once(dirname(__FILE__)."/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/helper/suivantPrecedent.php");
require_once( PASTELL_PATH ."/lib/journal/Journal.class.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once (PASTELL_PATH . "/include/navigation_collectivite.php");
require_once( PASTELL_PATH . "/lib/document/Document.class.php");

$recuperateur = new Recuperateur($_GET);
$offset = $recuperateur->getInt('offset',0);
$id_e = $recuperateur->getInt('id_e',0);
$type = $recuperateur->get('type');
$id_d = $recuperateur->get('id_d');


$liste_collectivite = array();

if ($id_e == 0){
	$liste_collectivite = $roleUtilisateur->getEntite($authentification->getId(),'journal:lecture');

	if ( ! $liste_collectivite){
		header("Location: ". SITE_BASE . "/index.php");
		exit;
	}

	if (count($liste_collectivite) == 1){
		$id_e = $liste_collectivite[0];
	}
	
} else if  (! $roleUtilisateur->hasDroit($authentification->getId(),"journal:lecture",$id_e)){
	header("Location: ".SITE_BASE . "index.php");
	exit;
}


$entite = new Entite($sqlQuery,$id_e);
$infoEntite = $entite->getInfo();



$count = $journal->countAll($id_e,$type,$id_d);


$page_title="Journal des évènements";
if ($id_e){
	$page_title .= " - ".$infoEntite['denomination'];
}
if ($type){
	$page_title .= " - " . $type;
}
if ($id_d) {
	
	$document = new Document($sqlQuery);
	$documentInfo = $document->getInfo($id_d);
	
	$page_title .= " - " . $documentInfo['titre'];
}

$limit = 20;
$all = $journal->getAll($id_e,$type,$id_d,$offset,$limit) ;

include( PASTELL_PATH ."/include/haut.php");

?>
<?php if ($id_d) : ?>
<a href='journal/index.php?id_e=<?php echo $id_e?>'>« Journal de <?php echo $infoEntite['denomination']?></a>
<?php endif;?>
<?php if ($roleUtilisateur->hasDroit($authentification->getId(),"journal:lecture",$id_e)) : 
suivant_precedent($offset,$limit,$count,"journal/index.php?id_e=$id_e");

?>
<div class="box_contenu clearfix">

<h2>Journal des évènements (extraits)</h2>

<table class="tab_01">
	<tr>
		<th>Numéro</th>
		<th>Date</th>
		<th>Type</th>
		<th>Entité</th>
		<th>Utilisateur</th>
		<th>Document</th>
		<th>Action</th>
		<th>Message</th>
		<th>Horodatage</th>
	</tr>
<?php foreach($all as $i => $ligne) : ?>
	<tr  class='<?php echo $i%2?'bg_class_gris':'bg_class_blanc'?>'>
		<td><a href='journal/detail.php?id_j=<?php echo $ligne['id_j'] ?>'><?php echo $ligne['id_j']?></a></td>
		<td><?php echo  $ligne['date']?></td>
		<td><?php echo $journal->getTypeAsString($ligne['type']) ?></td>
		<td><a href='entite/detail.php?id_e=<?php echo $ligne['id_e'] ?>'><?php echo  $ligne['denomination']?></a></td>
		<td><a href='utilisateur/detail.php?id_u=<?php echo  $ligne['id_u']?>'><?php echo $ligne['prenom'] . " " . $ligne['nom']?></a></td>
		<td>
			<a href='document/detail.php?id_d=<?php echo $ligne['id_d']?>&id_e=<?php echo $ligne['id_e']?>'>
				<?php echo $ligne['titre']?>
			</a>
		</td>
		<td><?php echo  $ligne['action']?></td>
		
		<td><?php echo $ligne['message']?></td>
		<td><?php if ($ligne['preuve']) : ?> 
			<?php echo $ligne['date_horodatage'] ?>
			<?php else : ?>
			en cours
			<?php endif;?>
		</td>
	</tr>
<?php endforeach;?>
</table>
</div>
<?php endif;?>
<?php 

if (!$id_e && ! $roleUtilisateur->hasDroit($authentification->getId(),"journal:lecture",$id_e) ){
	navigation_racine($liste_collectivite,"journal/index.php?a=a");
} else {
	navigation_collectivite($entite,"journal/index.php?a=a");
}
include( PASTELL_PATH ."/include/bas.php");
