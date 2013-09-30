<?php
class Array2XML {

	private $domDocument;
	
	public function getXML($root_tag, array $array){
		ob_start();
		echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		echo "<$root_tag>";
		$this->addArray($array,$root_tag);
		echo "</$root_tag>";
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}
	

	private function addArray(array $array,$tag){
		foreach ($array as $cle => $value) {
			if (is_int($cle)) {
				echo "<$tag id='$cle'>";
			} else {
				echo "<$cle>";
			}
			if (is_array($value)){
				$this->addArray($value,$cle);
			} else {
				echo htmlspecialchars($value);
			}
			if (is_int($cle)) {
				echo "</$tag>";
			} else {
				echo "</$cle>";
			}
			
		}
	}
	
}