<?php 

class AideControler extends PastellControler {
	
	public function indexAction(){
		
		$this->page_title = "Aide";
		$this->template_milieu = "AideIndex";
		$this->renderDefault();
		
	}
	
}