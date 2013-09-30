<?php

class MegalisRecup extends ActionExecutor {
	
	public function go(){		
		$megalis = $this->getMyConnecteur();
		
		$entiteListe = new EntiteListe($this->getSQLQuery());		
		
		$recup = $megalis->recup();
		if (! $recup){
			$this->setLastMessage($megalis->getLastError());
			return false;	
		}
		
		$file_result = array();
		if ($recup['file_ignored']){
			$file_result["Fichiers ignorés"] = $recup['file_ignored'];
		}
				
		$document = $this->getDocument();
		
	
		
		foreach($recup['file_ok'] as $file_name){
			$siren = $megalis->getSiren($file_name); 
			$entite_info = $entiteListe->getBySiren($siren);
			if (!$entite_info){
				$file_result['Numéro SIREN inconnu'][] = $file_name;
				continue;
			}
			$id_e = $entite_info[0]['id_e'];
			
			$id_d = $document->getIdFromTitre($file_name,'megalis');
			
			if ($id_d){
				$file_result['Fichier déjà traité'][] = $file_name;
				continue;
			}
		
			if (! $this->retrieveDocument($id_e,$file_name,$megalis)){
				$file_result['Fichiers traités en erreur'][] = $file_name;
				continue;
			}
			
			$this->getNotificationMail()->notify($id_e,$id_d,'recuperation', $this->type,"Fichier récupérer sur le serveur Mégalis");							
			$file_result['Fichiers traités'][] = $file_name;	
		}
		
		$message = "";
		foreach($file_result as $type_file => $all_file) {
			$message .= "$type_file : " . implode(', ',$all_file)."<br/>";
		}
		
		$this->setLastMessage($message);
		return true;
	}
	
	private function retrieveDocument($id_e,$file_name,Megalis $megalis){
		
		
		$document =  $this->getDocument();
		$id_d = $document->getNewId();	
		$document->save($id_d,'megalis');
		$document->setTitre($id_d, $file_name);
		$documentEntite = new DocumentEntite($this->getSQLQuery());
		$documentEntite->addRole($id_d,$id_e,"editeur");
		$actionCreator = new ActionCreator($this->getSQLQuery(),$this->getJournal(),$id_d);
		$actionCreator->addAction($id_e,0,'recuperation',"Document récupéré sur le serveur megalis");
		$donneesFormulaire = $this->getDonneesFormulaireFactory()->get($id_d,'megalis');
		$donneesFormulaire->addFileFromData('archive',$file_name,"");			
		$archive_path = $donneesFormulaire->getFilePath('archive');
			
		$md5_content = $megalis->retrieveFile($file_name,$archive_path);
			
		if (! $md5_content){
			$actionCreator->addAction($id_e,0,'recuperation-erreur',$megalis->getLastError());
			$this->getNotificationMail()->notify($id_e,$id_d,'recuperation-erreur', $this->type,$megalis->getLastError());				
			return false;
		}
		$donneesFormulaire->setData('empreinte', $md5_content);
		
		$bordereau = $this->retrieveBordereau($donneesFormulaire->getFilePath('archive'));
		if ( ! $bordereau){
			$actionCreator->addAction($id_e,0,'recuperation-erreur',$this->getLastMessage());
			$this->getNotificationMail()->notify($id_e,$id_d,'recuperation-erreur', $this->type,$this->getLastMessage());				
			return false;
		}
		$donneesFormulaire->addFileFromData('bordereau', "bordereau.xml", $bordereau);		
		
		$donneesFormulaire->addFileFromData('fichier_attache',"archive.zip","");
		if (! $this->createFichierAttache($donneesFormulaire->getFilePath('archive'),$donneesFormulaire->getFilePath('fichier_attache'))){
			$donneesFormulaire->removeFile('fichier_attache');
			$actionCreator->addAction($id_e,0,'recuperation-erreur',$this->getLastMessage());
			$this->getNotificationMail()->notify($id_e,$id_d,'recuperation-erreur', $this->type,$this->getLastMessage());				
			return false;
		}
		
		$id_transfert = $this->getTransfertId($bordereau);
		if ( ! $id_transfert){
			$actionCreator->addAction($id_e,0,'recuperation-erreur',$this->getLastMessage());
			$this->getNotificationMail()->notify($id_e,$id_d,'recuperation-erreur', $this->type,$this->getLastMessage());				
			return false;
		}
		$donneesFormulaire->setData('transfert_id', $id_transfert);
		
		return true;
	}
	
	public function retrieveBordereau($path){	
		$zip = new ZipArchive();

		
		if (! $zip->open($path)) {
			$this->setLastMessage("Impossible d'ouvrir le fichier d'archive");
			return false;
		}
		$bordereau = @ $zip->getFromName('archive.xml');
		@ $zip->close();
		if (!$bordereau){
			$this->setLastMessage("L'archive ne contient pas de fichier archive.xml");
			return false;
		}
		//Suppression de la référence à la feuille de style.
		$bordereau = preg_replace("#<\?xml-stylesheet [^>]*>\n#",'',$bordereau);
				
		return $bordereau;
	}
	
	private function createFichierAttache($archive_path,$fichier_attache_path){		
		
		
		$passwordGenerator = new PasswordGenerator();
		$tmp_dir = $passwordGenerator->getPassword();
		$zip = new ZipArchive();
		$zip->open($archive_path);
		$zip->extractTo("/tmp/$tmp_dir/");
		$zip->close();
		
		rrmdir("/tmp/$tmp_dir/styles");
		@ unlink("/tmp/$tmp_dir/archive.xml");
		@ unlink("/tmp/$tmp_dir/archive.xsl");
		
		$myZip = new MyZipArchive();
		$myZip->zipDir("/tmp/$tmp_dir/", "/tmp/$tmp_dir.zip");
		
		rename("/tmp/$tmp_dir.zip",$fichier_attache_path);
		rrmdir("/tmp/$tmp_dir");		
		return true;
	}
	
	private function getTransfertId($bordereau){
		$xml = simplexml_load_string($bordereau);
		return strval($xml->TransferIdentifier);	
	}
	
}