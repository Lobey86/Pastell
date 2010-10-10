<?php

require_once( PASTELL_PATH . "/lib/document/DocumentEntite.class.php");

function liste_document(DocumentType $documentType,array $listDocument,$my_id_e) {
	
	
	$type = array();
	foreach($listDocument as $doc){
		$type[$doc['type']] = $doc['type'];
		
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
				<th>Dernier état</th>
				<th>Date</th>
			</tr>
		
		<?php 
		foreach($listDocument as $i => $document ) : ?>
			<tr class='<?php echo ($i++)%2?'bg_class_gris':'bg_class_blanc'?>'>
			
				<td>
					<?php if ($documentType->getAction($document['type'])->getProperties($document['last_action'],'accuse_de_reception_action')) :?>
						L'expediteur a demandé un accusé de réception : 
						<form action='document/action.php' method='post'>
						<input type='hidden' name='id_d' value='<?php echo $document['id_d'] ?>' />
						<input type='hidden' name='id_e' value='<?php echo $my_id_e ?>' />
						<input type='hidden' name='page' value='0' />
							
						<input type='hidden' name='action' value='<?php echo $documentType->getAction($document['type'])->getProperties($document['last_action'],'accuse_de_reception_action') ?>' />
							
						<input type='submit' value='Envoyer un accusé de réception'/>
						</form>
					<?php else :?>

					<a href='document/detail.php?id_d=<?php echo $document['id_d']?>&id_e=<?php echo $document['id_e']?>'>
						<?php echo $document['titre']?$document['titre']:$document['id_d']?>
					</a>			
					<?php endif;?>
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
				<td>
					<?php echo $documentType->getAction($document['type'])->getActionName($document['last_action']) ?>
				</td>
				<td>
					<?php echo $document['last_action_date']?>
				</td>
			</tr>
		<?php endforeach;?>
		</table>
						
		
	</div>
	<?php 
	
}