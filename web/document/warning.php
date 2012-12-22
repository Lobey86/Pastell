<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");

$recuperateur = new Recuperateur($_GET);
$id_d = $recuperateur->get('id_d');
$action = $recuperateur->get('action');
$id_e = $recuperateur->get('id_e');
$page = $recuperateur->getInt('page',0);

$document = $objectInstancier->Document;

$infoDocument = $document->getInfo($id_d);

$type = $infoDocument['type'];
$documentType = $documentTypeFactory->getFluxDocumentType($type);
$theAction = $documentType->getAction();

$actionName = $theAction->getDoActionName($action);

$page_title= "Attention ! Action irréversible";
include( PASTELL_PATH ."/include/haut.php");
?>
<a href='document/detail.php?id_d=<?php echo $id_d?>&id_e=<?php echo $id_e?>&page=<?php echo $page?>'>« <?php echo $infoDocument['titre']?></a>
<br/><br/>
<div class='box_alert'>
	<p>L'action «<?php echo $actionName ?>» est irréversible.</p>
</div>



<div class="box_contenu clearfix">
			<h2>Etes-vous sûr de vouloir effectuer cette action ? </h2>
			
			
			<form action='document/action.php' method='post'>
			<input type='hidden' name='id_d' value='<?php echo $id_d ?>' />
				<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />
				<input type='hidden' name='page' value='<?php echo $page ?>' />			
				<input type='hidden' name='action' value='<?php echo $action ?>' />
				<input type='hidden' name='go' value='1' />
				<input type='submit' value='<?php echo $actionName?>' />
			</form>
			
</div>
<?php 
include( PASTELL_PATH ."/include/bas.php");
