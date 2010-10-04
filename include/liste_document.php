<?php

require_once( PASTELL_PATH . "/lib/document/DocumentEntite.class.php");

function liste_document(DocumentType $documentType,array $listDocument,$my_id_e) {
	
	
	$tabEntete = array();
	$type = array();
	
	$i = 0;
	
	foreach($listDocument as $doc){
		foreach($doc['action'] as $action => $date){
			if (! in_array($action,$tabEntete)){
				$tabEntete[] = $action;
			}
		}
		$type[$doc['type']] = 1;
	}
	$type = array_keys($type);
	
	global $sqlQuery;
	$documentEntite = new DocumentEntite($sqlQuery);
	
	
	?>
		<div class="box_contenu clearfix">
	
		<h2>Documents <?php if (count($type) == 1) echo  $documentType->getName($type[0]) ?> </h2>
			<table class="tab_01">
			<tr>
				<th>Objet</th>
				<?php if (count($type) > 1 ): ?>
					<th>Type</th>
				<?php endif;?>
				<th>Entité</th>
				<?php foreach($tabEntete as $entete) : ?>
					<th><?php echo $entete?></th>
				<?php endforeach;?>
			</tr>
		
		<?php 
		foreach($listDocument as $document ) : ?>
			<tr class='<?php echo ($i++)%2?'bg_class_gris':'bg_class_blanc'?>'>
			
				<td>
					<a href='document/detail.php?id_d=<?php echo $document['id_d']?>&id_e=<?php echo $document['id_e']?>'>
						<?php echo $document['titre']?$document['titre']:$document['id_d']?>
					</a>			
				</td>
				<?php if (count($type) > 1 ): ?>
					<td><?php echo  $documentType->getName($document['type'])?></td>
				<?php endif;?>
				<td>
				<?php 
				$listeEntite = $documentEntite->getEntite( $document['id_d']);
				foreach($listeEntite as $docEntite){
					if ($docEntite['id_e'] == $my_id_e){
						$my_role = $docEntite['role'];
					}
				}
				
				foreach($listeEntite as $docEntite) : 
					if (($my_role == 'editeur' || $docEntite['role'] == 'editeur') && ($docEntite['id_e'] != $my_id_e)) : 
				?>
				<a href='entite/detail.php?id_e=<?php echo $docEntite['id_e']?>'><?php echo $docEntite['denomination']?></a><br/>
				<?php endif;?>
				<?php endforeach;?>
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
	<?php 
	
}