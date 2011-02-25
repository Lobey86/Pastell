<?php


class DocumentTypeHTML {
	
	private function getOption($documentTypeFactory){
		?>
			<option value=''>Tous les types de document</option>
				<?php foreach($documentTypeFactory->getAllType() as $flux_type => $lesFlux ) : ?>
					<?php foreach($lesFlux as $type => $description): ?>
					<option value='<?php echo $type?>'> <?php echo $description ?> </option>
					<?php endforeach ; ?>
				<?php endforeach ; ?>
		<?php 
	}
	
	public function displaySelect($documentTypeFactory){ 
		?>
		<select name='type'>
			<?php $this->getOption($documentTypeFactory) ?>
		</select>
		<?php 
	}
	
	public function displaySelectWithCollectivite($documentTypeFactory){
		?>
		<select name='type'>
			<?php $this->getOption($documentTypeFactory) ?>
			<option value='collectivite-properties'> Collectivite </option>
		</select>
		<?php 
	}
	
	
}