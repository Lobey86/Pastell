<?php

class DocumentTypeHTML {
	
	private $allDroit;
	private $fluxDefinitionFiles;
	
	public function __construct(FluxDefinitionFiles $fluxDefinitionFiles){
		$this->fluxDefinitionFiles = $fluxDefinitionFiles;
	}
	
	public function setDroit($allDroit){
		$this->allDroit = $allDroit;
	}
	
	private function getType(){
		if ($this->allDroit){
			$result =  $this->fluxDefinitionFiles->getTypeByDroit($this->allDroit);		
			return $result;
		}
		$allType = $this->fluxDefinitionFiles->getAllType();
		foreach($allType as $flux_type => $lesFlux){
			foreach($lesFlux as $type => $description){
				$result[$type] = $description;
			}
		}
		return $result;
	}
	
	private function getOption($type_selected=""){
		?>
			<option value=''>Tous les types de document</option>
				<?php foreach($this->getType() as $type => $description) : ?>
					<option value='<?php echo $type?>' <?php echo $type_selected==$type?"selected='selected'":""?>>
					<?php echo $description ?>
					</option>
				<?php endforeach ; ?>
		<?php 
	}
	
	public function displaySelect($type_selected=""){ 
		?>
		<select name='type'>
			<?php $this->getOption($type_selected) ?>
		</select>
		<?php 
	}
	
	public function displaySelectWithCollectivite(){
		?>
		<select name='type'>
			<?php $this->getOption() ?>
			<option value='collectivite-properties'>Collectivite</option>
		</select>
		<?php 
	}
	
	
}