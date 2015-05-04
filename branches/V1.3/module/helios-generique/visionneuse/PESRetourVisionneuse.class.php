<?php
class PESRetourVisionneuse extends Visionneuse {
	
	
	private function hecho($text){
		hecho(utf8_decode($text));
	}
	
	private function getDomaineLibelle($libelle_numero){
		$libelle_list = array("technique","technique","technique","validité du certificat","pièce justificative","dépense","recette","budget");
		return $libelle_list[$libelle_numero];
	}
	
	public function display($filename,$filepath){
		
		$xml = simplexml_load_file($filepath);
		
		
		$nomFic = $xml->Enveloppe->Parametres->NomFic['V'];
		
		$nb_erreur = 0;
		if( !empty($xml->ACQUIT->ElementACQUIT) ) {
			foreach($xml->ACQUIT->ElementACQUIT as $elementACQUIT){
				if ($elementACQUIT->EtatAck['V'] != 1){
					$nb_erreur ++;
				}
			}
		}
				
		
		
		header("Content-type: text/html; charset=iso8859-1");
		?>
<style>
.pes_retour{
	border-style: solid;
    border-width: thin;
    padding: 5px;
}
</style>
<div class='pes_retour'>
	<h1>Rapport acquittement</h1>
	<p>
		<b>Identification du flux : </b><?php echo $nomFic?>
	</p>
	<p class="libelleControl">
		<?php echo count ($xml->ACQUIT->ElementACQUIT)?> éléments
		<?php if ($nb_erreur > 0) : ?>
			 &nbsp;-&nbsp;<b style='color:red'><?php echo $nb_erreur?> erreur<?php echo $nb_erreur>1?'s':''?></b>
		<?php endif;?>
	</p>
	<?php if( !empty($xml->ACQUIT->ElementACQUIT) ) :?>
	<table>
		<tr>
			<th>Domaine</th>
			<th>Exercice</th>
			<th>Numéro de bordereau</th>
			<th>Acquitté</th>
			<th>Erreur</th>
		</tr>
		<?php foreach($xml->ACQUIT->ElementACQUIT as $elementACQUIT) : ?>
		<tr>
			<td>
				<?php echo $this->getDomaineLibelle(strval($elementACQUIT->DomaineAck['V']))?>
			</td>
			<td><?php hecho($elementACQUIT->ExerciceBord['V'])?></td>
			<td><?php hecho($elementACQUIT->NumBord['V'])?></td>
			<td>
				<?php if ($elementACQUIT->EtatAck['V'] == 1) : ?>
					<b style='color:green'>OUI</b>
				<?php else: ?>
					<b style='color:red'>NON</b>
				<?php endif;?>
			</td>
			<td>
				<?php if ($elementACQUIT->EtatAck['V'] == 1) : ?>
					&nbsp;
				<?php else: ?>
					 <b>Erreur <?php hecho($elementACQUIT->Erreur->NumAnoAck['V']) ?> : 
				<?php $this->hecho($elementACQUIT->Erreur->LibelleAnoAck['V'])?> 
			<?php if (strval($elementACQUIT->NumPiece['V'])) : ?>
			sur pièce n° <?php hecho($elementACQUIT->NumPiece['V'])?>
			<?php endif;?>  
			<?php if (strval($elementACQUIT->NumLigne['V'])) : ?>
			, ligne n° <?php hecho($elementACQUIT->NumLigne['V'])?></b>
			<?php endif;?>
				<?php endif;?>
			</td>
		
		</tr>
		<?php endforeach;?>
	</table>
	<?php endif;?>
</div>
		<?php 
	}
}