<?php


class DocumentTypeHTML {
	
	private $allDroit;
	
	public function setDroit($allDroit){
		$this->allDroit = $allDroit;
	}
	
	private function getType($documentTypeFactory){
		if ($this->allDroit){
			$result =  $documentTypeFactory->getTypeByDroit($this->allDroit);
		
			return $result;
		}
		$allType = $documentTypeFactory->getAllType();
		foreach($allType as $flux_type => $lesFlux){
			foreach($lesFlux as $type => $description){
				$result[$type] = $description;
			}
		}
		return $result;
	}
	
	private function getOption($documentTypeFactory,$type_selected=""){
		?>
			<option value=''>Tous les types de document</option>
				<?php foreach($this->getType($documentTypeFactory) as $type => $description) : ?>
					<option value='<?php echo $type?>' <?php echo $type_selected==$type?"selected='selected'":""?>>
					<?php echo $description ?>
					</option>
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