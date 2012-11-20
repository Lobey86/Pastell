<?php

class EntiteDetailHTML {
	
	private $droitEdition;
	private $droitLectureCDG;
	
	public function addDroitEdition(){
		$this->droitEdition = true;
	}

	public function addDroitLectureCDG(){
		$this->droitLectureCDG = true;
	}
	
	public function display(array $entiteExtendedInfo,$entiteProperties,$lastTransaction =false){
		$id_e = $entiteExtendedInfo['id_e'];
		?>
		<h2>Informations générales
			<?php if ($this->droitEdition) : ?>
			<a href="entite/edition.php?id_e=<?php echo $id_e?>" class='btn_maj'>
					Modifier
				</a>
			<?php endif;?>
		</h2>
		<table class='tab_04'>		
			<tr>
				<th>Type</th>
				<td><?php echo Entite::getNom($entiteExtendedInfo['type']) ?></td>
			</tr>
			
			<tr>
				<th>Dénomination</th>
				<td><?php echo $entiteExtendedInfo['denomination'] ?></td>
			</tr>
			<?php if ($entiteExtendedInfo['siren']) : ?>
				<tr>
					<th>Siren</th>
					<td><?php echo $entiteExtendedInfo['siren'] ?></td>
				</tr>
			<?php endif;?>
			<?php if ($entiteExtendedInfo['type'] == Entite::TYPE_FOURNISSEUR ) : ?>
				<tr>
				<th>Etat</th>
				
				<td>
				<?php if($lastTransaction) : ?>
				<a href='<?php echo SITE_BASE ?>flux/detail-transaction.php?id_t=<?php echo $lastTransaction; ?>'>
				<?php endif;?>
				<?php echo Entite::getChaineEtat($entiteExtendedInfo['etat']) ?> 
				<?php if($lastTransaction) : ?>
				</a>
				<?php endif;?>
				
				</td>
				</tr>
			<?php endif;?>
			<tr>
				<th>Date d'inscription</th>
				<td><?php echo time_iso_to_fr($entiteExtendedInfo['date_inscription']) ?></td>
			</tr>
			<?php if ($entiteExtendedInfo['entite_mere']) : ?>
			<tr>
				<th>Entité mère</th>
				<td>
					<a href='entite/detail.php?id_e=<?php echo $entiteExtendedInfo['entite_mere']['id_e']?>'>
						<?php echo $entiteExtendedInfo['entite_mere']['denomination'] ?>
					</a>
				</td>
			</tr>
			<?php endif;?>
			<?php if ($entiteExtendedInfo['type'] != Entite::TYPE_FOURNISSEUR ) : ?>
				<tr>
				<th>Entité fille</th>
				<td>
					<?php if ( ! $entiteExtendedInfo['filles']) : ?>
						<?php echo "Cette entité n'a pas d'entité fille"?>
					<?php endif;?>
					<ul>
					<?php foreach($entiteExtendedInfo['filles'] as $fille) : ?>
						<li><a href='entite/detail.php?id_e=<?php echo $fille['id_e']?>'>
							<?php echo $fille['denomination']?>
						</a></li>
					<?php endforeach;?>
					</ul>
					<?php if ($this->droitEdition) : ?>
						<a href="entite/edition.php?entite_mere=<?php echo $id_e?>" >
							Ajouter une entité fille
						</a>
						<br/>
						<a href="entite/import.php?id_e=<?php echo $id_e?>" >
							Importer des entités filles
						</a>
					<?php endif;?>
				</td>
				</tr>
			<?php endif;?>
			<?php if ($entiteExtendedInfo['cdg']) : 
				$infoCDG = $entiteExtendedInfo['cdg'];
			
			?>
				<tr>
					<th>Centre de gestion</th>
					<td>
						<?php if ($this->droitLectureCDG ) : ?>			
							<a href='entite/detail.php?id_e=<?php echo $infoCDG['id_e']?>'>
								<?php echo $infoCDG['denomination']?>
							</a>
						<?php else : ?>
							<?php echo $infoCDG['denomination']?>
						<?php endif; ?>
						
						</td>
				</tr>
			<?php endif;?>
			
			<tr>
			<th>GED</th>
			<td><?php echo $entiteProperties->getProperties(EntiteProperties::ALL_FLUX,'has_ged') ?></td>
			</tr>
			
			<tr>
			<th>SAE</th>
			<td><?php echo $entiteProperties->getProperties(EntiteProperties::ALL_FLUX,'has_archivage') ?></td>
			</tr>
			
		</table>
	<?php 
	}
	
}