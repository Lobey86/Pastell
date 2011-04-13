<?php


class DocumentTypeHTML {
	
	private function getOption($documentTypeFactory,$type_selected=""){
		?>
			<option value=''>Tous les types de document</option>
				<?php foreach($documentTypeFactory->getAllType() as $flux_type => $lesFlux ) : ?>
					<?php foreach($lesFlux as $type => $description): ?>
					<option value='<?php echo $type?>' <?php echo $type_selected==$type?"selected='selected'":""?>>
						<?php echo $description ?>
						</option>
					<?php endforeach ; ?>
				<?php endforeach ; ?>
		<?php 
	}
	
	public function displaySelect($documentTypeFactory,$type_selected=""){ 
		?>
		<select name='type'>
			<?php $this->getOption($documentTypeFactory,$type_selected) ?>
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