<?php 

class ModifFacture extends ActionExecutor {
	
	public function go(){
		$this->page= 3;
		header("Location: edition.php?id_d={$this->id_d}&id_e={$this->id_e}&page={$this->page}&action={$this->action}");
		exit;
	}
	
}