<?php 
require_once( ZEN_PATH . "/lib/ZenMail.class.php");

class MailVerification {
	
	public function __construct(ZenMail $zenMail){
		$this->zenMail = $zenMail;
	}
	
	public function send($infoFournisseur){
		
		$this->zenMail->setEmmeteur("Pastell","pastell@sigmalis.com");
		$this->zenMail->setDestinataire($infoFournisseur['email']);
		$this->zenMail->setSujet("Votre inscription sur Pastell");
		
		ob_start();
?>Bienvenue sur Pastell ! 

Pour continuer la procédure d'inscription vous devez cliquez sur le lien ci-dessous :
<?php echo SITE_BASE ?>inscription/fournisseur/mail-verification.php?login=<?php echo urlencode($infoFournisseur['login']); ?>&password=<?php echo urlencode($infoFournisseur['mail_verif_password'])?>

Cordialement.		

<?php 
		$content=ob_get_contents();
		ob_end_clean();
		
		$this->zenMail->setContenu($content);
		$this->zenMail->send();
	}
	
}