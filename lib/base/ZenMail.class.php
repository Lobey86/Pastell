<?php 
require_once("ZLog.class.php");

class ZenMail{
	
	const DEFAULT_CHARSET = 'ISO-8859-15';
	
	private $zLog;
	
	private $destinataire;
	private $sujet;
	private $contenu;
	
	private $emmeteur;
	private $mailEmmeteur;
	
	private $charset;
	
	public function __construct(ZLog $zLog = null){
		if (! $zLog){
			$zLog = new ZLog();
		}
		$this->zLog = $zLog;
		$this->setCharset(self::DEFAULT_CHARSET);
	}
	
	public function setCharset($charset){
		$this->charset = $charset;
	}
	
	public function setEmmeteur($nom,$mail){
		$this->emmeteur = "$nom <$mail>";
		$this->mailEmmeteur = $mail; 
	}
	
	public function setDestinataire($destinataire){
		$this->destinataire = $destinataire;
	}
	
	public function setSujet($sujet){
		$this->sujet =  "=?iso-8859-15?Q?$sujet?=";
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
	
	public function send(){
		assert('$this->emmeteur');
		assert('$this->mailEmmeteur');
		assert('$this->destinataire');
		assert('$this->sujet');
		assert('$this->contenu');		
		
		$entete =	"From: ".$this->emmeteur."\r\n".
					"Reply-To: ".$this->mailEmmeteur."\r\n".
					"Content-Type: text/plain; charset=\"".$this->charset."\"";		
		
		$this->zLog->log("Envoi d'un mail",ZLog::DEBUG);
		$this->zLog->log("Destinataire : ".$this->destinataire,ZLog::DEBUG);
		$this->zLog->log("Sujet : ".$this->sujet,ZLog::DEBUG);
		$this->zLog->log("Entete: ".$entete,ZLog::DEBUG);
		$this->zLog->log("Contenu: ".$this->contenu,ZLog::DEBUG);		
		
    	mail($this->destinataire,$this->sujet,$this->contenu,$entete);
   
	}	
    
}