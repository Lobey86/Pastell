<?php

require_once(__DIR__."/../../connecteur-type/TdtConnecteur.class.php");

class StelaException extends TdTException {}


class Stela extends TdtConnecteur {
	
	const WSDL_ACTES = "ws-miat.wsdl"; 
	const WSDL_HELIOS = "ws-helios.wsdl";
	
	private $tdt_server_certificate;
	private $tdt_user_certificat_pem;
	private $tdt_user_certificat;
	private $tdt_user_certificat_password;
	private $tdt_user_certificat_and_key_pem;
	

	public function setConnecteurConfig(DonneesFormulaire $collectiviteProperties){				
		$this->tdt_server_certificate = $collectiviteProperties->getFilePath('server_certificate');	
		$this->tdt_user_certificat = $collectiviteProperties->getFilePath('user_certificat');
		$this->tdt_user_certificat_pem = $collectiviteProperties->getFilePath('user_certificat_pem');
		$this->tdt_user_certificat_password = $collectiviteProperties->get('user_certificat_password');
		$this->tdt_user_certificat_and_key_pem = $collectiviteProperties->get('user_certificat_and_key_pem');
		$this->tedetisURL = $collectiviteProperties->get('url');
		$this->classificationFile = $collectiviteProperties->getFilePath('classification_file');
	}
	

	public function getLogicielName(){
		return "Stela";
	}
	
	private function getSoapClient($wsdl){
		static $soapClient;
		if ($soapClient){
			return $soapClient;
		}
		$certificat_content = file_get_contents($this->tdt_user_certificat_pem);
		$certificat = new Certificat($certificat_content);
		
		$options= 	array(
			'compression'=>true,
			'exceptions'=>true,
			'trace'=>true,
			'local_cert' => $this->tdt_user_certificat_and_key_pem,
			'passphrase' => $this->tdt_user_certificat_password,
			'encoding'=>'UTF-8'
			);
			
			
		$soapClient = new SoapClient($this->tedetisURL."/$wsdl", $options);

		$authHeader["SSLCertificatSerial"]= $certificat->getSerialNumber(); ;
		$authHeader["SSLCertificatVendor"]= $certificat->getIssuer();
	
		$header=new SoapHeader($this->tedetisURL,'authHeader',$authHeader); 
		$soapClient->__setSoapHeaders($header);
		return $soapClient;
	}
	
	private function soapCall($function_name,array $args,$wsdl = false){
		if ( ! $wsdl ){
			$wsdl = self::WSDL_ACTES;
		}
		$soapClient = $this->getSoapClient($wsdl);
		$result = $soapClient->__soapCall($function_name,$args);
		$result = json_decode($result, true);
		if ($result[0] == "OK"){
			return $result[1];
		} 
		throw new StelaException($result[1]);
		return false;
	}
	
	public function getUID($wsdl=false){
		return $this->soapCall('connexionSTELA',array('uid'),$wsdl); 
	}
	
	public function testConnexion(){
		$result = $this->getUID();
		if ($result){
			$result = "UID = $result";
		}
		return $result;
	}
	
	public function getClassification(){
		$uid = $this->getUID();
		$classification_stela = $this->soapCall('getResultatFormMiat',array($uid));
		
		$RetourClassification = new ZenXML('actes:RetourClassification');
		$RetourClassification['xmlns:actes'] = "http://www.interieur.gouv.fr/ACTES#v1.1-20040216"; 
		$RetourClassification['xmlns:insee'] = "http://xml.insee.fr/schema";
		$RetourClassification['xmlns:xsi'] = "http://www.w3.org/2001/XMLSchema-instance";
		$RetourClassification['xsi:schemaLocation']="http://www.interieur.gouv.fr/ACTES#v1.1-20040216 actesv1_1.xsd";
		
		$RetourClassification->set("actes:DateClassification",$classification_stela["collectivite_dateClassification"]);
		
		
		$naturesActes = $RetourClassification->get("actes:NaturesActes");
		$natureActe = $naturesActes->get("actes:NatureActe");
		$i = 0;
		foreach($classification_stela['natureActes'] as $num_nature => $type_nature){
			$natureActe[$i]['actes:CodeNatureActe'] = $num_nature;
			$natureActe[$i]['actes:Libelle'] = $type_nature;
			$i++;
		}
		
		foreach($classification_stela['codeMatiere'] as $all_code => $all_libelle){
			$code_array = explode("-",$all_code);
			$libelle_array = explode("/",$all_libelle);
			$matiere[$code_array[0]]['libelle'] = trim($libelle_array[0]);			
			$matiere[$code_array[0]][$code_array[1]]['libelle'] = trim($libelle_array[1]);
			if ($code_array[2] != 0){
				$matiere[$code_array[0]][$code_array[1]][$code_array[2]]['libelle'] = trim($libelle_array[2]);
			}
			if ($code_array[3] != 0){
				$matiere[$code_array[0]][$code_array[1]][$code_array[2]][$code_array[3]]['libelle'] = trim($libelle_array[3]);
			}
			if ($code_array[4] != 0){
				$matiere[$code_array[0]][$code_array[1]][$code_array[2]][$code_array[3]][$code_array[4]]['libelle'] = trim($libelle_array[4]);
			}
		}
		
		$actes_matieres = $RetourClassification->get("actes:Matieres");
		$this->rempliClassifRecursif($matiere, $actes_matieres,1);
		
		return $RetourClassification->asXML();
	}
	
	private function rempliClassifRecursif($matiere_stela,$matiere_element,$num_niveau){
		$i = 0;
		foreach($matiere_stela as $code_matiere => $matiere){
			if (! intval($code_matiere)){
				continue;
			}
			$actes_matiere = $matiere_element->get("actes:Matiere$num_niveau");
			$actes_matiere[$i]['actes:CodeMatiere']= $code_matiere;
			$actes_matiere[$i]['actes:Libelle']= utf8_decode(utf8_decode($matiere['libelle']));
			$this->rempliClassifRecursif($matiere, $actes_matiere[$i],$num_niveau + 1);
			$i++;
		}
	}
	
	private function getBase64FileContent($file_path){
		return base64_encode(file_get_contents($file_path));
	}
	
	public function postActes(DonneesFormulaire $donneesFormulaire) {
		$uid = $this->getUID();
		$classification_stela = $this->soapCall('getResultatFormMiat',array($uid));
		$groupId = $classification_stela['grantSendGroups'][0]['groupid'];
		
		$acte_info = array();
		$file_name = $donneesFormulaire->get('arrete');
		$file_name = $file_name[0];
		
		$acte_info['fichier'][]= array(
			"base64"=> $this->getBase64FileContent($donneesFormulaire->getFilePath('arrete')),
			"name" => $file_name
		);
		
		if ($donneesFormulaire->get('autre_document_attache')){
				foreach($donneesFormulaire->get('autre_document_attache') as $i => $file_name){
					$file_path = $donneesFormulaire->getFilePath('autre_document_attache',$i);
					$acte_info['fichier'][]	= array(
						"base64"		=>  $this->getBase64FileContent($file_path),
						"name"		=> $file_name
					);			
				}
			}
			
		$classification  = array(0,0,0,0,0);
		$c1 = explode(" ",$donneesFormulaire->get('classification'));
		$c1[0];
		foreach(explode(".",$c1[0]) as $i=>$nb){
			$classification[$i] = $nb;
		}
		$classification = implode("-",$classification);
			
		$acte_info['informations']	= array(
			"uid"		=> $uid, 
			"groupSend"	=> $groupId,
			"dateDecision"	=> date("Y/m/d", strtotime($donneesFormulaire->get('date_de_lacte'))), 
			"numInterne"	=> $donneesFormulaire->get('numero_de_lacte'), 
			"natureActe"	=> $donneesFormulaire->get('acte_nature'), 
			"matiereActe"	=> $classification, 
			"objet"		=> $donneesFormulaire->get('objet'),
			"numInterneOld"	=> ""
		);

		$result = $this->soapCall('putActe',array(json_encode($acte_info))); 
		if (! $result){
			return false;
		}
		$donneesFormulaire->setData('tedetis_transaction_id',$result['miat_ID']);
		return true;		
	}
	
	public function getStatus($id_transaction){
		$result = $this->soapCall('getDetailsActe',array($id_transaction));
		if (! $result){
			return false;
		}
		if ($result['anomalies']){
			return parent::STATUS_ERREUR;
		}
		if ($result['dateAR']){
			return parent::STATUS_ACQUITTEMENT_RECU;
		}
		return parent::STATUS_TRANSMIS;
	}
	
	public function getARActes(){
		//Stela ne fourni pas l'accusé de réception Actes du ministère
		return false;
	}

	public function getDateAR($id_transaction){
		$result = $this->soapCall('getDetailsActe',array($id_transaction));
		return $result['dateAR'];
	}

	public function getBordereau($id_transaction){
		$result = $this->soapCall('getDocument',array($id_transaction,1));
		if (! $result){
			return false;
		}
		$document = base64_decode($result['chaine_fichier']);
		return $document;
	}
	
	public function demandeClassification(){
		throw new StelaException("STELA ne propose pas cette fonctionalité.");
	}
	
	public function annulationActes($id_transaction){
		$result = $this->soapCall('annulationActe',array($id_transaction)); 
		if (!$result){
			return false;
		}
		return true;
	}
	
	public function verifClassif(){
		throw new StelaException("Not implemented");
	}
	
	public function postHelios(DonneesFormulaire $donneesFormulaire){
		
		$uid = $this->soapCall('connexionSTELA',array('uid'),self::WSDL_HELIOS);
		if (!$uid){
			return false;
		}
		$result = $this->soapCall('getResultatFormHelios',array($uid),self::WSDL_HELIOS);
		foreach($result as $id => $info){
			$group_id = $id;
			break;
		}
		if (! $group_id){
			return false; 
		}
		
		$helios_info['informations']	= array(
					"uid"		=> $uid, 
					"groupSend"	=> $group_id,
					"title" => $donneesFormulaire->get('objet'),
					"comment" => "Fichier Helios posté via Pastell",
		);

		$file_path = $donneesFormulaire->getFilePath('fichier_pes_signe');
		
		
		$helios_info['fichier']['name'] = $this->getHeliosFileName($file_path);
		$helios_info['fichier']['base64'] = $this->getBase64FileContent($file_path);
		
		
		$idDocument = $this->soapCall('putPESAller',array(json_encode($helios_info)),self::WSDL_HELIOS);
		
		if( ! $idDocument ){
			return false;
		}	
		$donneesFormulaire->setData('tedetis_transaction_id',$idDocument);
		return true;
	}
	
	private function getHeliosFileName($pes_path){
		$xml = simplexml_load_file($pes_path);
		
		$type_fic = $xml->Enveloppe->Parametres->TypFic['V'];
		$id_col = $xml->EnTetePES->IdColl['V'];
		$date_str = $xml->EnTetePES->DteStr['V'];
		$num_ordre = "01";
		
		return "{$type_fic}_{$id_col}_{$date_str}_{$num_ordre}.xml";
	}
	
	
	public function getStatusHelios($id_transaction){
		throw new StelaException("Not implemented");
	}
	
	public function getLastReponseFile(){
		throw new StelaException("Not implemented");	
	}
	
	public function getStatusInfo($status_id){
		throw new StelaException("Not implemented");
	}
	
	public function getFichierRetour($transaction_id){
		throw new StelaException("Not implemented");
	}
	
}
