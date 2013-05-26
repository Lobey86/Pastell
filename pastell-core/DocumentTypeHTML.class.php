<?php

//TODO a mettre dans template
class DocumentTypeHTML {

	private function getOption($type_selected="",$all_module = array()){
		?>
		<option value=''>Tous les types de document</option>
		<?php foreach($all_module as $type => $module_by_type) : ?>
			<optgroup label="<?php hecho($type) ?>">
			<?php foreach($module_by_type as $module_id => $module_description) :?>
				<option value='<?php echo $module_id?>' <?php echo $type_selected==$module_id?"selected='selected'":""?>>
				<?php echo $module_description ?>
				</option>
			<?php endforeach;?>
			</optgroup>
		<?php endforeach ; ?>
		<?php 
	}
	
	public function displaySelect($type_selected="",$all_module = array()){ 
		?>
		<select name='type'>
			<?php $this->getOption($type_selected,$all_module) ?>
		</select>
		<?php 
	}
	
	public function displaySelectWithCollectivite($all_module = array()){
		?>
		<select name='type'>
			<?php $this->getOption("",$all_module) ?>
			<option value='collectivite-properties'>Collectivite</option>
		</select>
		<?php 
	}
	
}