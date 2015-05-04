<?php 
class ZenMail {
	
	const DEFAULT_CHARSET = 'ISO-8859-15';

	//const HEADER_LINE_ENDING =
	
	private $fileContentType;
	
	private $destinataire;
	private $sujet;
	private $contenu;
	private $image;
	
	private $emmeteur;
	private $mailEmmeteur;
	
	private $charset;
	
	private $attachment;
	
	public function __construct(FileContentType $fileContentType){
		$this->setCharset(self::DEFAULT_CHARSET);
		$this->image = array();
		$this->fileContentType = $fileContentType;
	}
	
	public function setCharset($charset){
		$this->charset = $charset;
	}
	
	public function setEmetteur($nom,$mail){
		$this->emmeteur = "$nom <$mail>";
		$this->mailEmmeteur = $mail; 
	}
	
	public function setDestinataire($destinataire){
		$this->destinataire = $destinataire;
	}
	
	public function setSujet($sujet){
		if (imap_8bit($sujet) == $sujet){
			$this->sujet = $sujet;
		} else {
			$this->sujet = "=?UTF-8?Q?".strtr(imap_8bit(utf8_encode($sujet)),' ','_')."?=";
		}
	}
	
	public function setContenu($script,$info){
		ob_start();
			include($script);
			$this->contenu = ob_get_contents();
		ob_end_clean();
	}	
	
	
	public function setContenuText($content){
		$this->contenu = $content;
	}
	
	public function resetAttachment(){
		$this->attachment = array();
	}
	
	public function addAttachment($filename,$filepath){
		$this->attachment[$filename] = $filepath;
	}	
	
	public function send(){
		assert('$this->emmeteur');
		assert('$this->mailEmmeteur');
		assert('$this->destinataire');
		assert('$this->sujet');
		assert('$this->contenu');		
		
		if ($this->attachment) {
			$this->sendTxtMailWithAttachment();
		} else {
			$entete =	"From: ".$this->emmeteur.PHP_EOL.
						"Reply-To: ".$this->mailEmmeteur.PHP_EOL.
						"Content-Type: text/plain; charset=\"".$this->charset."\"";		
	    	mail($this->destinataire,$this->sujet,$this->contenu,$entete);
		}   
	}	
	
	private function sendTxtMailWithAttachment(){
		$boundary = $this->getBoundary();
		$entete =	"From: ".$this->emmeteur.PHP_EOL.
				"Reply-To: ".$this->mailEmmeteur.PHP_EOL.
				"MIME-Version: 1.0".PHP_EOL.
				"Content-Type: multipart/mixed; boundary=\"$boundary\"";
		
		$message = "This is a multi-part message in MIME format".PHP_EOL.PHP_EOL;
		
		$message .= "--".$boundary.PHP_EOL .
		"Content-Type: text/plain; charset=\"".$this->charset."\"".PHP_EOL.
		"Content-Transfer-Encoding: 8bit".PHP_EOL.
		PHP_EOL.
		$this->contenu.PHP_EOL.PHP_EOL;
		
		foreach($this->attachment as $filename => $filepath){
			$content_type = $this->fileContentType->getContentType($filepath); 
			$message.="--".$boundary.PHP_EOL;
			$message.=
			"Content-Type: $content_type; name=\"$filename\"".PHP_EOL. 
			"Content-Transfer-Encoding: base64".PHP_EOL. 
			"Content-Disposition: attachment, filename=\"$filename\"".PHP_EOL.PHP_EOL;
			
			$attachment = chunk_split(base64_encode(file_get_contents($filepath)),76,PHP_EOL);
			$message.=$attachment;			
		} 
		$message .= "--".$boundary.PHP_EOL;
		
		mail($this->destinataire,$this->sujet,$message,$entete);
	}
	
	
	
	
	private function getBoundary(){
		return '_pastell_zen_mail_' .
				substr(sha1( 'ZenMail' . microtime()), 0, 12);
	}
	
	private function getTxtAlternative($html_content){
			$search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
					'@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
					'@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
					'@<![\s\S]*?--[ \t\n\r]*>@'        // Strip multi-line comments i
			);
			return preg_replace($search, '', $html_content);
	}
	
	
	public function addRelatedImage($filename,$file_path){
		$this->image[$filename] = $file_path;
	}
	
	public function sendHTMLContent($html_content,$charset="iso-8859-15",$txt_alternative=false){
		
		if (! $txt_alternative){
			$txt_alternative = $this->getTxtAlternative($html_content);
		}
		
		$boundary = $this->getBoundary();
		$boundary_related = $this->getBoundary();
		
		$entete =	"From: ".$this->emmeteur.PHP_EOL.
					"Reply-To: ".$this->mailEmmeteur.PHP_EOL.
					"MIME-Version: 1.0".PHP_EOL.
					"Content-Type: multipart/alternative; boundary=\"$boundary\"";
		
		$message = "--".$boundary.PHP_EOL .
					"Content-Type: text/plain; charset=\"".$this->charset."\"".PHP_EOL.
					"Content-Transfer-Encoding: 8bit".PHP_EOL.
					PHP_EOL.
					$txt_alternative.PHP_EOL.
					PHP_EOL.
					"--".$boundary.PHP_EOL.
					"Content-Type: multipart/related; boundary=\"$boundary_related\"".PHP_EOL.
					PHP_EOL.
					"--".$boundary_related.PHP_EOL.
					"Content-Type: text/html; charset=\"".$this->charset."\"".PHP_EOL.
					"Content-Transfer-Encoding: 8bit".PHP_EOL.
					PHP_EOL.
					$html_content.PHP_EOL.
					PHP_EOL;
					$i = 0;
					foreach($this->image as $filename => $filepath){
						
						$content_type = $this->fileContentType->getContentType($filepath);
						
						$message .= "--".$boundary_related.PHP_EOL.
									 "Content-type: $content_type; filename=\"$filename\"".PHP_EOL.
									 "Content-ID: <image$i>".PHP_EOL.
									 "Content-transfer-encoding: base64".PHP_EOL.
									 "Content-Disposition: inline, filename=\"$filename\"".PHP_EOL.
									 PHP_EOL.
									 chunk_split(base64_encode(file_get_contents($filepath)));
						$i++;
					}
		$message .= 
					"--".$boundary_related.PHP_EOL;
					PHP_EOL.
					"--".$boundary.PHP_EOL;
					
		mail($this->destinataire,$this->sujet,$message,$entete);
	}
}