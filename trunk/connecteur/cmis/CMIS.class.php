<?php

class CMIS {
	
	const NS_CMIS  = "http://docs.oasis-open.org/ns/cmis/core/200908/";
	const NS_CMIS_RA = "http://docs.oasis-open.org/ns/cmis/restatom/200908/";
	
	private $url;
	private $login;
	private $password;
	
	private $lastError;
	
	public function setConnecteurConfig(DonneesFormulaire $collectiviteProperties){
		$this->url = $collectiviteProperties->get('ged_url');
		$this->login =$collectiviteProperties->get('ged_user_login');
		$this->password = $collectiviteProperties->get('ged_user_password');
		$this->folder = $collectiviteProperties->get('ged_folder');
	}
	
	public function getRepositoryRetrieveInfo(){
		return array('repositoryId','repositoryName','repositoryDescription','vendorName','productName','productVersion','rootFolderId');
	}
	
	public function getFolderRetrieveInfo(){
		return array('content','id','summary','title','published','updated');
	}
	
	public function getLastError(){
		return $this->lastError;
	}
	
    private function get($url,$content = false){
    	$session = curl_init($url);
        curl_setopt($session, CURLOPT_HEADER, false);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($session, CURLOPT_USERPWD, $this->login . ":" . $this->password);

        if ($content){
        	curl_setopt($session, CURLOPT_CUSTOMREQUEST, "POST");
        	curl_setopt($session, CURLOPT_POSTFIELDS, $content);
            curl_setopt($session, CURLOPT_HTTPHEADER, array ("Content-Type: application/atom+xml;type=entry"));
        } else {
			curl_setopt($session, CURLOPT_CUSTOMREQUEST, "GET");
        }
        $result =  curl_exec($session);
        $codeResponse = curl_getinfo($session, CURLINFO_HTTP_CODE);
         
        if ($codeResponse != 200){
        	$this->lastError = curl_error($session);
        	if (! $this->lastError){
        		$this->lastError = "Erreur $codeResponse (la GED a retourné : $result)";
        	}
        	return false;
        }
        
      	return $result;
    }
	 
	public function getRepositoryInfo(){
		$xmldata = $this->get($this->url);
		if (! $xmldata){
			return false;
		}
		
		libxml_use_internal_errors(true);
		$xml = simplexml_load_string($xmldata);	
		
		if (! $xml){
			$errors = libxml_get_errors();
			$this->lastError = "Erreur XML : ".$errors[0]->message;
			return false;
		}
		
		
		
		$repInfo = $xml->workspace->children(self::NS_CMIS_RA)->repositoryInfo;
		
		$result = array();
		foreach($this->getRepositoryRetrieveInfo() as $infoName){
			$result[$infoName] = strval($repInfo->children(self::NS_CMIS)->$infoName);
		}
		
		$uriTemplate = $xml->workspace->children(self::NS_CMIS_RA)->uritemplate;
		
		foreach ($uriTemplate as $template){
			$type = strval($template->children(self::NS_CMIS_RA)->type);
			$result['template'][$type] = strval($template->children(self::NS_CMIS_RA)->template);
		}
		
		return $result;
	}
	
	public function testObject(){
		return $this->getObjectByPath($this->folder);
	}
	
	public function getObjectByPath($path)
    {
    	$repositoryInfo = $this->getRepositoryInfo();
    	
    	if (! $repositoryInfo){
    		return false;
    	}
    	
    	$url_template = $repositoryInfo['template']['objectbypath'];
    	
    	$url = str_replace("{path}", $path, $url_template);
    	$url = preg_replace("/{[a-zA-Z0-9_]+}/", "", $url);
    	
        $xmldata = $this->get($url);
        
        if (! $xmldata){
        	return false;
        }
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmldata);	
   		if (! $xml){
			$errors = libxml_get_errors();
			$this->lastError = "Erreur XML : ".$errors[0]->message;
			return false;
		}
        
    	
		$result = array('author' => strval($xml->author->name));
		foreach($this->getFolderRetrieveInfo() as $infoName){
			$result[$infoName] = strval($xml->$infoName);
		}
        
		foreach($xml->link as $link){
			if (! isset($result['link'][strval($link['rel'])])){
				$result['link'][strval($link['rel'])] = strval($link['href']);
			}
		}
		
        return $result;        
    }
	
	
	public function addDocument($folder,$documentPath){
		$folderInfo = $this->getObjectByPath($folder);
		$folderId = $folderInfo['id'];

        $url = $folderInfo['link']['down'];      
        
        $content = $this->getContent("titre4","Ce document contient plein de chose","Ceci est du contenu");

        $ret = $this->get($url, $content);

        return $ret;
	}
	
	public function getContent($title,$summary,$content) {
        ob_start();
        echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
?>
<atom:entry xmlns:cmis="http://docs.oasis-open.org/ns/cmis/core/200908/" 
			xmlns:cmism="http://docs.oasis-open.org/ns/cmis/messaging/200908/"
			xmlns:atom="http://www.w3.org/2005/Atom"
			xmlns:app="http://www.w3.org/2007/app"
			xmlns:cmisra="http://docs.oasis-open.org/ns/cmis/restatom/200908/">
	<atom:title><?php echo $title ?></atom:title>
	<atom:summary><?php echo $summary ?></atom:summary>
	<cmisra:content>
		<cmisra:mediatype><?php echo "text/plain"?></cmisra:mediatype>
		<cmisra:base64>
<?php echo base64_encode($content);?>
		</cmisra:base64>
	</cmisra:content>
	<cmisra:object>
		<cmis:properties>
			<cmis:propertyId propertyDefinitionId="cmis:objectTypeId">
				<cmis:value>cmis:document</cmis:value>
			</cmis:propertyId>
		</cmis:properties>
	</cmisra:object>
</atom:entry>
<?php
        return ob_get_clean();
    }
    
	
}