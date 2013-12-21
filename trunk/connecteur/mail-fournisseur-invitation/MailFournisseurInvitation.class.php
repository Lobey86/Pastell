<?php

class MailFournisseurInvitation extends Connecteur {
	
	private $zenMail;
	
	private $subject;
	private $from;
	private $from_libelle;
	private $charset;
	private $content_html;
	private $content_txt;
	private $embeded_image;
	
	public function __construct(ZenMail $zenMail){
		$this->zenMail = $zenMail;
	}
	
	public function setConnecteurConfig(DonneesFormulaire $entiteConfig){
		$this->subject = $entiteConfig->get('subject');
		$this->from = $entiteConfig->get('from');
		$this->from_libelle = $entiteConfig->get('from_libelle');
		$this->charset = $entiteConfig->get('charset');
		$this->content_html = $entiteConfig->getFileContent("content_html");
		$this->content_txt = $entiteConfig->getFileContent("content_txt");
		foreach($entiteConfig->get('embeded_image') as $i => $filename){
			$this->embeded_image[$filename] = $entiteConfig->getFilePath("embeded_image",$i);	
		}
	}
	
	public function send(DonneesFormulaire $documentData, array $entiteInfo){
		$to = $documentData->get("email");
		
		foreach(array('raison_sociale','email') as $key){
			$replacement["%FLUX:$key%"] = $documentData->get($key);
		}
		
		foreach(array('denomination','siren') as $key){
			$replacement["%ENTITE:$key%"] = $entiteInfo[$key];
		}
		
		
		$content_html = $this->content_html;
		$content_txt = $this->content_txt;
		foreach($replacement as $key => $value){
			$content_html = preg_replace("#$key#", $value, $content_html);
			$content_txt = preg_replace("#$key#", $value, $content_txt);
		}
		
		
		foreach($this->embeded_image as $filename => $file_path){
			$this->zenMail->addRelatedImage($filename, $file_path);
		}
		
		$this->zenMail->setEmetteur($this->from_libelle, $this->from);
		$this->zenMail->setDestinataire($to);
		$this->zenMail->setSujet($this->subject);
		$this->zenMail->setCharset($this->charset);
		$this->zenMail->sendHTMLContent($content_html,$content_txt);
		return true;
	}
	
	
}