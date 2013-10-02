<?php
class Test extends ActionExecutor {
	
	
	public function go(){
		$action_name  = $this->getActionName();
		echo $action_name;
		$acte_nature = $this->getFormulaire()->getField('acte_nature')->getSelect();
		print_r($acte_nature);
	}
	
}