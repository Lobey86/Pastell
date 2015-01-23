<?php
class FournisseurFactureEnvoie extends ActionExecutor {
	
	public function go(){
		$infoEntite = $this->getEntite()->getInfo();
		$emmeteurName = $infoEntite['denomination'];
		
		$id_e_collectivite = $this->getDonneesFormulaire()->get('id_e_collectivite');
		$infoCollectivite = $this->objectInstancier->EntiteSQL->getInfo($id_e_collectivite);
		$denomination_col = $infoCollectivite['denomination'];
		
		$this->getDocumentEntite()->addRole($this->id_d,$id_e_collectivite,"lecteur");
				
		$actionCreator = $this->getActionCreator();
		$actionCreator->addAction($this->id_e,$this->id_u,'envoi', "La facture a été envoyée  à $denomination_col");
		$actionCreator->addToEntite($id_e_collectivite,"La facture a été envoyée par $emmeteurName");
			
		$actionCreator->addAction($id_e_collectivite,0,'recu', "La facture a été reçue ");
		$actionCreator->addToEntite($this->id_e,"La facture a été reçue par $denomination_col");
			
		$message = $emmeteurName. " vous envoie une facture.\n\n";
		$message.= "Pour la consulter : " . SITE_BASE . "document/detail.php?id_d={$this->id_d}&id_e=$id_e_collectivite";
			
		$this->getNotificationMail()->notify($id_e_collectivite,$this->id_d, $this->action, $this->type,$message);
		
		$this->setLastMessage("Facture envoyé à $denomination_col");
		return true;
	}
	
}