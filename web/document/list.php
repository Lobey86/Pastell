<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once (PASTELL_PATH . "/lib/document/Document.class.php");

$recuperateur = new Recuperateur($_GET);

$type = $recuperateur->get('type');
$siren = $recuperateur->get('siren',0);

$document = new Document($sqlQuery);

$page_title = "Liste des documents $type";

if ($roleUtilisateur->hasDroit($authentification->getId(),$type.":edition",$siren)){
	$nouveau_bouton_url = "document/edition.php?type=$type";
}

include( PASTELL_PATH ."/include/haut.php");
?>


<div class="box_contenu clearfix">

<h2>Document </h2>
				
<ul>
	<?php foreach($document->getAll($type) as $f) : ?>
		<li><a href='document/voir.php?id_d=<?php echo $f['id_d']?>'><?php echo $f['id_d']?></a></li>
	<?php endforeach;?>
</ul>
</div>

<?php
include( PASTELL_PATH ."/include/bas.php");
