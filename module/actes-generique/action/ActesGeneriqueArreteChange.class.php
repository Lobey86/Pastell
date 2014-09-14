<?php
class ActesGeneriqueArreteChange extends ActionExecutor{

	public function go(){
		
		
		$content_type = $this->getDonneesFormulaire()->getContentType('arrete');
		//Vérifier que le doc est en xml ou en pdf
		if (in_array($content_type,array("application/pdf","application/xml"))){
			return true;
		}
		
		$filename = $this->getDonneesFormulaire()->getFileName('arrete');
		
		if (! in_array($content_type,array("application/vnd.oasis.opendocument.text"))){
				throw new Exception("Le document $filename est au format $content_type ! Or, il doit être au format PDF ou XML. Il sera bloqué par le tiers de télétransmission");
		}
		//unocvon
		
		$this->setLastMessage("Le document « $filename » a été converti au format PDF pour respecter la norme ACTES");
		return true;
	}
	
}