<?php
require_once( PASTELL_PATH . "/lib/action/ActionExecutor.class.php");
require_once( PASTELL_PATH . "/lib/entite/EntiteListe.class.php");
require_once( PASTELL_PATH . "/lib/connecteur/megalis/Megalis.class.php");

class MegalisRecup extends ActionExecutor {
	
	public function go(){
		
		$entiteListe = new EntiteListe($this->getSQLQuery());
		
		
		$megalis = new Megalis($this->getDonneesFormulaire(),new SSH2());
		$recup = $megalis->recup();
		if (! $recup){
			$this->setLastMessage($megalis->getLastError());
			return false;	
		}
		
		$siren_not_found = array();
		$file_ok = array();
		$file_error = array();
		$file_deja_traite = array();
		
		foreach($recup['file_ok'] as $file_name){
			$siren = $megalis->getSiren($file_name); 
			$entite_info = $entiteListe->getBySiren($siren);
			if (!$entite_info){
				$siren_not_found[] = $file_name;
				continue;
			}
			$id_e = $entite_info[0]['id_e'];
			
			$document = new Document($this->getSQLQuery());
			
			$id_d = $document->getIdFromTitre($file_name,'megalis');
			if ($id_d){
				$file_deja_traite[] = $file_name;
				continue;
			}
			
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
				$file_error[] = $file_name;
				continue;
			}
			$donneesFormulaire->setData('empreinte', $md5_content);
			$this->getNotificationMail()->notify($id_e,$id_d,'recuperation', $this->type,"Fichier récupérer sur le serveur Mégalis");				
			
			$file_ok[] = $file_name;	
		}
		
		$message = "";
		$message .= $this->addTabToMessage($recup['file_ignored'], "Fichiers ignorés");
		$message .= $this->addTabToMessage($siren_not_found, "Numéro SIREN inconnu");
		$message .= $this->addTabToMessage($file_error, "Fichiers traités en erreur");
		$message .= $this->addTabToMessage($file_deja_traite, "Fichier déjà traité");
		$message .= $this->addTabToMessage($file_ok, "Fichiers traités");
		
		
		$this->setLastMessage($message);
		return true;
	}
	
	private function addTabToMessage(array $all_file,$title){
		if (! $all_file){
			return "";
		}
		return "$title : " . implode(', ',$all_file)."<br/>";
	}
	
}