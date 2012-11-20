<?php

class TransactionListe {
	
	private $showTypeFlux;
	private $afficheEtatAvance;
	
	public function setAfficheEtatAvance(){
		$this->afficheEtatAvance = true;
	}
	
	public function showTypeFlux(){
		$this->showTypeFlux = true;
	}
	
	public function affiche(TransactionFinder $transactionFinder){	
		$allTransaction = $transactionFinder->getTransaction();
		
		
		$colonne = $transactionFinder->getAllState();
		?>
				
		<table class="tab_01">
			<tr>
				<th>Entité</th>
				<th>Objet</th>
				<?php if ( $this->showTypeFlux) : ?>
						<th>Type</th>
				<?php endif; ?>
				<?php foreach ($colonne as $intitule) : ?>
					<th><?php echo $intitule ?></th>
				<?php endforeach; ?>
				
			</tr>
		
		<?php $i = 0;
		
		foreach($allTransaction as $id_t => $transaction) : ?>
			<tr class='<?php echo ($i++)%2?'bg_class_gris':'bg_class_blanc'?>'>
				<td>
					<?php foreach($transaction['role'] as $role) : ?>
					<a href='entite/detail.php?siren=<?php echo $role['siren']?>'>
						<?php echo $role['denomination']?>
					</a>&nbsp;
					<?php endforeach;?>
		
				
				</td>
				<td>
					<a href='flux/detail-transaction.php?id_t=<?php echo $transaction['id_t']?>'>
						<?php echo $transaction['objet']?$transaction['objet']:$transaction['id_t']?>
					</a>			
				</td>
				<?php if ( $this->showTypeFlux) : ?>
					<td>
						<?php echo FluxFactory::getTitreS($transaction['type']) ?>
					</td>
				<?php endif;?>
				<?php foreach ($colonne as $intitule) : ?>
					<td><?php echo isset($transaction['state'][$intitule])?$transaction['state'][$intitule]:'' ?></td>
				<?php endforeach; ?>
			</tr>
		<?php endforeach;?>
		</table>
	<?php 
	}
}