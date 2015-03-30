<?php
require_once(PASTELL_PATH."/ext/Akita_JOSE/JWS.php");
require_once(PASTELL_PATH."/ext/phpseclib/Crypt/Hash.php");
require_once(PASTELL_PATH."/ext/phpseclib/Crypt/RSA.php");
require_once(PASTELL_PATH."/ext/phpseclib/Math/BigInteger.php");
require_once(PASTELL_PATH."/ext/base64url.php");

class OpenIDAuthentication extends Connecteur {
	
	const PASTELL_OPENID_SESSION_TOKEN = "pastell_open_id_session_token";
	const PASTELL_OPENID_SESSION_NONCE = "pastell_open_id_session_nonce";
	const PASTELL_OPENID_SESSION_ACCESS_TOKEN = "pastell_open_id_access_token";
	const PASTELL_OPENID_SESSION_ID_TOKEN = "pastell_open_id_token";
	
	private $open_id_url;
	private $client_id;
	private $client_secret;
	private $curlWrapper;
	private $url_callback;
	private $site_index;
	
	private $connecteurFactory;
	
	/**
	 * 
	 * @param CurlWrapper $curlWrapper
	 * @param string $url_callback URL pour le retour de l'authentification OpenID
	 */
	public function __construct(CurlWrapper $curlWrapper,$open_id_url_callback,ConnecteurFactory $connecteurFactory){
		$this->curlWrapper = $curlWrapper;
		$this->url_callback = $open_id_url_callback;
		$this->site_index = SITE_BASE;
		$this->connecteurFactory = $connecteurFactory;
	}
	
	public function setConnecteurConfig(DonneesFormulaire $donneesFormulaire){
		$this->client_id = $donneesFormulaire->get("client_id");
		$this->client_secret = $donneesFormulaire->get("client_secret");
		
		
		$oasisProvisionning = $this->connecteurFactory->getGlobalConnecteurConfig('oasis-provisionning');
		if (! $oasisProvisionning){
			throw new Exception("Impossible de trouver un connecteur de provisionning OASIS");
		}
		$open_id_url = $oasisProvisionning->get('open_id_url');
		if (! $open_id_url){
			throw new Exception("Impossible de trouver une URL pour OpenID");
		}
		
		$this->open_id_url = $open_id_url;
	}
	
	
	
	private function getRandomToken(){
		return sha1(mt_rand(0,mt_getrandmax()));
	}
	
	public function authenticate(){
		$connecteur_info = $this->getConnecteurInfo();
		$_SESSION[self::PASTELL_OPENID_SESSION_TOKEN] = $this->getRandomToken();
		$state = "id_ce={$connecteur_info['id_ce']}&token={$_SESSION[self::PASTELL_OPENID_SESSION_TOKEN]}";
		
		$_SESSION[self::PASTELL_OPENID_SESSION_NONCE] = $this->getRandomToken();
		
		$info=array("response_type"=>'code',
					"client_id"=> $this->client_id,
					"scope"=>"openid%20profile",
					"redirect_uri"=>$this->url_callback,
					"state"=>urlencode($state),
					"nonce"=>$_SESSION[self::PASTELL_OPENID_SESSION_NONCE]
		);
		
		$url = $this->open_id_url."/a/auth?";
		foreach($info as $key=>$value){
			$url.=$key."=".$value."&";
		}		
		header("Location: $url");
		exit;
	}
	
	public function getPublicKey(){
		$curlWrapper = new CurlWrapper();
		$curlWrapper->httpAuthentication($this->client_id, $this->client_secret);
		$key_contents = $curlWrapper->get($this->open_id_url."/a/keys");
		$key_contents = json_decode($key_contents);
		$key_contents = $key_contents->keys[0];
		
		
		$modulus = new Math_BigInteger('0x' . bin2hex(base64url_decode($key_contents->n)), 16);
		$exponent = new Math_BigInteger('0x' . bin2hex(base64url_decode($key_contents->e)), 16);
		
		
		$rsa = new Crypt_RSA();
		$rsa->modulus = $modulus;
		$rsa->exponent = $exponent;
		$rsa->publicExponent = $exponent;
		$rsa->k = strlen($rsa->modulus->toBytes());
		
		return $rsa->getPublicKey(CRYPT_RSA_PRIVATE_FORMAT_PKCS1);
	}
	
	public function returnAuthenticate(Recuperateur $recuperateur){
		$code = $recuperateur->get("code");
		$state = $recuperateur->get('state');
		$state = urldecode($state);
		$state_array = array();
		parse_str($state, $state_array);
		$token = $state_array['token'];		
		if ($token != $_SESSION[self::PASTELL_OPENID_SESSION_TOKEN]){
			throw new Exception("Le token ne correspond pas");
		}
		
		$error = $recuperateur->get('error');
		
		if ($error){
			
			header("Location: ".SITE_BASE."/oasis/connexion-return-error.php");
			exit;
			
		}
		
		$this->curlWrapper->httpAuthentication($this->client_id, $this->client_secret);
		
		$post_data = array(
				"grant_type" =>"authorization_code",
				"code" => $code,
				"redirect_uri" => $this->url_callback
		);
		
		$this->curlWrapper->setPostDataUrlEncode($post_data);
		$result = $this->curlWrapper->get($this->open_id_url."/a/token");
		if ($this->curlWrapper->getHTTPCode() != 200){
			if (! $result){
				$message_erreur = $this->curlWrapper->getLastError();
			} else {
				$result_array = json_decode($result,true);
				$message_erreur = $result_array['error'];
			}
			throw new Exception("Erreur lors de la récupération des infos sur le serveur OpenID : ".$message_erreur);
		}
		
		$result_array = json_decode($result,true);
		
		$id_token = $result_array['id_token'];
		$_SESSION[self::PASTELL_OPENID_SESSION_ID_TOKEN] = $id_token;
		
		
		$all_part = explode(".",$id_token);
		$header = json_decode(base64_decode($all_part[0]),true); 
		$payload = json_decode(base64_decode($all_part[1]),true);
		
		if ($payload['nonce'] != $_SESSION[self::PASTELL_OPENID_SESSION_NONCE]){
			throw new Exception("La nonce ne correspond pas");
		}
		
		unset($_SESSION[self::PASTELL_OPENID_SESSION_NONCE]);
		
		$jws = Akita_JOSE_JWS::load($id_token, true);
		
		$public_key = $this->getPublicKey();
		
		if(! $jws->verify($public_key)){
			echo "La vérification du JWT a échoué";
		}
		
		$_SESSION[self::PASTELL_OPENID_SESSION_ACCESS_TOKEN] = $result_array['access_token'];
				
		return $payload['sub'];
	}
	
	public function logout(){
		if (empty($_SESSION[self::PASTELL_OPENID_SESSION_ACCESS_TOKEN])){
			return false;
		}
		$this->curlWrapper->httpAuthentication($this->client_id, $this->client_secret);
		$post_data = array(
				"token" =>$_SESSION[self::PASTELL_OPENID_SESSION_ACCESS_TOKEN],
				"token_type_hint" => 'access_token'
		);
		$this->curlWrapper->setPostDataUrlEncode($post_data);
		$result = $this->curlWrapper->get($this->open_id_url."/a/revoke");
		if ($this->curlWrapper->getHTTPCode() != 200){
			if (! $result){
				$message_erreur = $this->curlWrapper->getLastError();
			} else {
				$result_array = json_decode($result,true);
				$message_erreur = $result_array['error'];
			}
			throw new Exception("Erreur lors de la récupération des infos sur le serveur OpenID : ".$message_erreur);
		}
		
		$info=array("id_token_hing"=>$_SESSION[self::PASTELL_OPENID_SESSION_ID_TOKEN],
				"post_redirect_uri"=>$this->site_index,
		);
		
		$url = $this->open_id_url."/a/logout?";
		foreach($info as $key=>$value){
			$url.=$key."=".$value."&";
		}
		session_destroy();
		header("Location: $url");
		exit;
	}
	
	private function checkHTTPResponse($result){
		$http_code = $this->curlWrapper->getHTTPCode(); 
		if ($http_code != 200){
			if (! $result){
				$message_erreur = $this->curlWrapper->getLastError();
			} else {
				$result_array = json_decode($result,true);
				$message_erreur = $result_array['error'];
			}
				
			throw new Exception("Erreur lors de la récupération des infos sur le serveur OpenID (HTTP code $http_code): ".$message_erreur);
		}
	}
	
	public function listAccount(){
		$this->curlWrapper->addHeader("Authorization", "Bearer ".$_SESSION[self::PASTELL_OPENID_SESSION_ACCESS_TOKEN]);
		$result = $this->curlWrapper->get($this->open_id_url."/apps/acl/instance/".$this->client_id);
		$this->checkHTTPResponse($result);
		$result = json_decode($result,true);
		return $result;
	}
		
	public function getUserInfo($user_id){
		$this->curlWrapper->addHeader("Authorization", "Bearer ".$_SESSION[self::PASTELL_OPENID_SESSION_ACCESS_TOKEN]);
		$result = $this->curlWrapper->get($this->open_id_url."/data?user=$user_id");
		$this->checkHTTPResponse($result);
		$result = json_decode($result,true);
		return $result;
	}
	
	
}