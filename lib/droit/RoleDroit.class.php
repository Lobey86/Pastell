<?php

class RoleDroit {
	
	const AUCUN_DROIT = 'aucun droit';
	
	private static $standard_role;
	
	public function __construct(){
		self::$standard_role = array(
					self::AUCUN_DROIT => array(),
					'admin' => array(
									'role:lecture',
									'role:edition',
									'entite:edition',
									'entite:lecture',
									'journal:lecture',
									'fournisseur:lecture',
									'utilisateur:lecture',
									'utilisateur:edition',
									'rh-messages:edition',
									'rh-messages:lecture',
									'gf-devis:lecture',
									'gf-devis:edition',
									'gf-facture:lecture',
									'gf-facture:edition',
									'gf-bon-de-commande:lecture',
									'gf-bon-de-commande:edition',
									'fournisseur-inscription:lecture',
									'fournisseur-inscription:edition',
									'fournisseur-message:lecture',
									'fournisseur-message:edition',
									'message-service:edition',
									'message-service:lecture',
									'actes:edition',
									'actes:lecture',
									'mailsec:edition',
									'mailsec:lecture',
									'annuaire:lecture',
									'annuaire:edition',
									'citoyen-courrier:lecture',
									'citoyen-courrier:edition',
									'helios:edition',
									'helios:lecture'
					),
					'lecteur' => array(
									'entite:lecture',
									'utilisateur:lecture',
								),
					'fournisseur' => array(
									'fournisseur-inscription:lecture',
									'fournisseur-inscription:edition',
									'fournisseur-message:lecture',
									'fournisseur-message:edition',
									'journal:lecture',
									'entite:lecture',
								),
								
								
					'agent collectivite' => array(
									
									'rh-messages:edition',
									'rh-messages:lecture',
									'entite:lecture',
									'journal:lecture',
									'actes:edition',
									'actes:lecture',
									'mailsec:edition',
									'mailsec:lecture',
									'annuaire:lecture',
									'utilisateur:lecture',
									'entite:lecture',
									'message-service:edition',
									'message-service:lecture',
									'citoyen-courrier:lecture',
									'citoyen-courrier:edition',
								),
								
					'agent centre de gestion' => array(

								'rh-messages:edition',
								'rh-messages:lecture',
								'entite:lecture',
								'journal:lecture',
								'actes:edition',
								'actes:lecture',
								'mailsec:edition',
								'mailsec:lecture',
								'annuaire:lecture',
								'utilisateur:lecture',
								'entite:lecture',
								'message-service:edition',
								'message-service:lecture',
							),		
								
					'testeur' => array('test:lecture','test:edition'),
					'gestionnaire_de_role' => array('role:lecture','role:edition')
			);
	}
	
	public function hasDroit($role,$droit){
		return  isset(self::$standard_role[$role]) && in_array($droit,self::$standard_role[$role]);
	}

	public function getAllRole(){
		return self::$standard_role;
	}
	
	public function getDroit($role){
		if (! isset(self::$standard_role[$role])){
			return array();
		}
		return self::$standard_role[$role];
	}
	
	public function getAllDroit(){
		$allDroit = array();
		foreach($this->getAllRole() as $role){
			$allDroit = array_merge($allDroit,$role);
		}
		$result = array_values(array_unique($allDroit));
		sort($result);
		return $result;
	}
	
	
}