<?php
//TODO probablement à revoir

require_once(dirname(__FILE__)."/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/helper/suivantPrecedent.php");
require_once( PASTELL_PATH ."/lib/Journal.class.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");

$recuperateur = new Recuperateur($_GET);
$offset = $recuperateur->getInt('offset',0);

$journal = new Journal($sqlQuery);

$limit = 20;

if ($authentification->isAdmin()){
	$all = $journal->getAll($offset,$limit) ;
	$count = $journal->countAll();
} else {
	$all = $journal->getAllTransactionBySiren($infoEntite['siren'],$offset,$limit);
	$count = $journal->countBySiren($infoEntite['siren']);
}


$page_title="Journal des transactions";

include( PASTELL_PATH ."/include/haut.php");

suivant_precedent($offset,$limit,$count);
?>

<div class="box_contenu clearfix">

<h2>Liste des dernières transactions traitées</h2>

<table class="tab_01">
	<tr>
		<th>Numéro</th>
		<th>Date</th>
		<th>Type</th>
		<th>Transaction</th>
		<th>Message</th>
		<th>Preuve</th>
	</tr>
<?php foreach($all as $i => $ligne) : ?>
	<tr  class='<?php echo $i%2?'bg_class_gris':'bg_class_blanc'?>'>
		<td><?php echo $ligne['id_j']?></td>
		<td><?php echo  $ligne['date']?></td>
		<td><?php echo $journal->getTypeAsString($ligne['type']) ?></td>
		<td>
			<a href='<?php echo SITE_BASE?>flux/detail-transaction.php?id_t=<?php echo $ligne['id_t']?>'>
				<?php echo $ligne['id_t']?>
			</a>
		</td>
		<td><?php echo $ligne['message']?></td>
		<td><a href='journal/preuve.php?id=<?php echo $ligne['id_j']?>'>voir</a></td>
	</tr>
<?php endforeach;?>
</table>
</div>
<?php 
include( PASTELL_PATH ."/include/bas.php");
