<?php 
require_once("ZLog.class.php");

class ZenMail{
	
	const DEFAULT_CHARSET = 'UTF-8';
	
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
		$this->sujet =  "=?utf-8?Q?".imap_8bit($sujet)."?=";
	}
	
	public function setContenu($contenu){
		$this->contenu = $contenu ;
		$this->contenu .= "\n\n---------------------------------------------\n";
		$this->contenu .= _("Cet email vous a été envoyé automatiquement, merci de ne pas y répondre.");
		$this->contenu .="\n----------------------------------------------\n";
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
		
		$this->zLog->log("Envoie d'un mail",ZLog::DEBUG);
		$this->zLog->log("Destinataire : ".$this->destinataire,ZLog::DEBUG);
		$this->zLog->log("Sujet : ".$this->sujet,ZLog::DEBUG);
		$this->zLog->log("Entete: ".$entete,ZLog::DEBUG);
		$this->zLog->log("Contenu: ".$this->contenu,ZLog::DEBUG);		
		
    	mail($this->destinataire,$this->sujet,$this->contenu,$entete);
   
	}	
    
}