<?php
require_once("MessageDevis.class.php");
require_once("MessageBonDeLivraison.class.php");
require_once("MessageFacture.class.php");
require_once("MessageArrete.class.php");
require_once("MessageInscriptionFournisseur.class.php");
require_once("MessageContrat.class.php");
require_once("MessageCDG.class.php");
require_once("MessageDemandeDevis.class.php");
require_once("MessageInscriptionAccepter.class.php");
require_once("MessageInscriptionRefuser.class.php");



class MessageFactory {
	
	public static function getInstance($type){
			
		switch($type){
			case 'devis': return new MessageDevis(); break;
			case 'demande_devis' : return new MessageDemandeDevis(); break;
			case 'bon_de_commande' : return new MessageBonDeCommande(); break;
			case 'bon_de_livraison' : return new MessageBonDeLivraison(); break;
			case 'facture' : return new MessageFacture(); break;
			case 'arrete' : return new MessageArrete(); break;
			case 'inscription_fournisseur': return new MessageInscriptionFournisseur(); break;
			case 'contrat' : return new MessageContrat(); break;
			case 'message_cdg' : return new MessageCDG(); break;
			case 'message' : return new Message(); break;
			case 'inscription_accepter' : return new MessageInscriptionAccepter(); break;
			case 'inscription_refuser' : return new MessageInscriptionRefuser(); break;

		}
	
	}
	
}