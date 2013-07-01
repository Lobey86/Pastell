			<div class="box">
		
			<h2>Documents <?php if (count($type_list) == 1) 
									echo  	$this->DocumentTypeFactory->getFluxDocumentType($type_list[0])->getName() ?> </h2>
				<table class="table table-striped">
				<tr>
					<th class='w140'>Objet</th>
					<?php if (count($type_list) > 1 ): ?>
						<th>Type</th>
					<?php endif;?>
					<th>Entité</th>
					<th>Dernier état</th>
					<th>Date</th>
				</tr>
			
			<?php
		
			foreach($listDocument as $i => $document ) : 			
				$documentType = $this->documentTypeFactory->getFluxDocumentType($document['type']);
			
				$action = $documentType->getAction();
				
			?>
				<tr>
				
					<td>
						<?php 
						if ( $action->getProperties($document['last_action'],'accuse_de_reception_action')) :
						?>
							L'expediteur a demandé un accusé de réception : 
							<form action='document/action.php' method='post'>
								<input type='hidden' name='id_d' value='<?php echo $document['id_d'] ?>' />
								<input type='hidden' name='id_e' value='<?php echo $my_id_e ?>' />
								<input type='hidden' name='page' value='0' />
									
								<input type='hidden' name='action' value='<?php echo $action->getProperties($document['last_action'],'accuse_de_reception_action') ?>' />
									
								<input type='submit' class='btn' value='Envoyer un accusé de réception'/>
							</form>
						<?php else :?>
	
						<a href='document/detail.php?id_d=<?php echo $document['id_d']?>&id_e=<?php echo $document['id_e']?>'>
							<?php echo $document['titre']?$document['titre']:$document['id_d']?>
						</a>			
						<?php endif;?>
					</td>
					<?php if (count($type_list) > 1 ): ?>
						<td><?php echo  $documentType->getName()?></td>
					<?php endif;?>
					<td>
					<?php if (isset($document['entite_base']) && ! $my_id_e) : ?>
						<a href='entite/detail.php?id_e=<?php echo $document['id_e']?>'><?php echo $document['entite_base']; ?></a>
					<?php endif;?>
					<?php foreach($document['entite'] as $entite) : ?>
						<a href='entite/detail.php?id_e=<?php echo $entite['id_e']?>'>
							<?php echo $entite['denomination']?>
						</a>
						<br/>
					<?php endforeach;?>
					</td>
					<td>
						<?php echo $action->getActionName($document['last_action_display']) ?>
					</td>
					<td>
						<?php echo time_iso_to_fr($document['last_action_date']) ?>
					</td>
				</tr>
			<?php endforeach;?>
			</table>
		</div>
