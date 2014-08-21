			<div class="box">
			<h2>Documents <?php if (count($type_list) == 1) 
									echo  	$this->DocumentTypeFactory->getFluxDocumentType($type_list[0])->getName() ?> </h2>
									
				<table class="table table-striped">
				<tr>
					<?php foreach($champs_affiches as $champs => $champs_libelle):?>
							<th>
								<?php if ( $url_tri && $champs!='dernier_etat') : ?>
							<a href='<?php echo $url_tri ?>&tri=<?php echo $champs?>&sens_tri=<?php echo ($champs==$tri)?($sens_tri=='ASC'?'DESC':'ASC'):$sens_tri ?>'>
							<?php hecho($champs_libelle)?></a>
							<?php else : ?>
							<?php hecho($champs_libelle)?>
							<?php endif;?>
							<?php if ($champs==$tri): ?>
								<?php if($sens_tri=='ASC'):?>
									<img src='img/commun/fleche-haut.png'/>
								<?php else: ?>
									<img src='img/commun/fleche-bas.png'/>
								<?php endif;?>
							<?php endif;?>
							</th>
					<?php endforeach;?>
				</tr>
				
			<?php
		
			foreach($listDocument as $i => $document ) : 			
				$documentType = $this->documentTypeFactory->getFluxDocumentType($document['type']);
				$action = $documentType->getAction();
				$formulaire = $documentType->getFormulaire();
				
			?>
				<tr>
					<?php foreach($champs_affiches as $champs=>$champs_libelle): ?>
						<td>
							<?php if($champs=='titre'):?>
								<?php  if ( $action->getProperties($document['last_action'],'accuse_de_reception_action')) : ?>
									L'expediteur a demandé un accusé de réception : 
									<form action='document/action.php' method='post'>
										<input type='hidden' name='id_d' value='<?php echo $document['id_d'] ?>' />
										<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />
										<input type='hidden' name='page' value='0' />
											
										<input type='hidden' name='action' value='<?php echo $action->getProperties($document['last_action'],'accuse_de_reception_action') ?>' />
											
										<input type='submit' class='btn' value='Envoyer un accusé de réception'/>
									</form>
								<?php else :?>
									<a href='document/detail.php?id_d=<?php echo $document['id_d']?>&id_e=<?php echo $document['id_e']?>'>
										<?php echo $document['titre']?$document['titre']:$document['id_d']?>
									</a>			
									<?php endif;?>
							<?php elseif ($champs=='type'):?>
								<?php echo  $documentType->getName()?>
							<?php elseif($champs=='entite'):?>
								<?php if (isset($document['entite_base']) && ! $id_e) : ?>
									<a href='entite/detail.php?id_e=<?php echo $document['id_e']?>'><?php echo $document['entite_base']; ?></a>
								<?php endif;?>
								<?php foreach($document['entite'] as $entite) : ?>
									<a href='entite/detail.php?id_e=<?php echo $entite['id_e']?>'>
										<?php echo $entite['denomination']?>
									</a>
									<br/>
								<?php endforeach;?>
							<?php elseif($champs=='dernier_etat') :?>
								<?php echo $action->getActionName($document['last_action_display']) ?>
							<?php elseif($champs=='date_dernier_etat') :?>
								<?php echo time_iso_to_fr($document['last_action_date']) ?>	
							<?php else:?>
							<?php if ($formulaire->getField($champs)->getType() == 'file') : ?>
								<a href='document/recuperation-fichier.php?id_d=<?php echo $document['id_d']?>&id_e=<?php echo $document['id_e']?>&field=<?php echo $champs?>&num=0'>
									<?php hecho($this->DocumentIndexSQL->get($document['id_d'],$champs));?>
								</a>
							<?php else:?>
								<?php hecho($this->DocumentIndexSQL->get($document['id_d'],$champs));?>
							<?php endif;?>											
							<?php endif;?>
											
						</td>
					<?php endforeach;?>
				</tr>
			<?php endforeach;?>
			</table>
		</div>
