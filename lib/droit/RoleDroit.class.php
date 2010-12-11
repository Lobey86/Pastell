<?php

class RoleDroit {
	
	const AUCUN_DROIT = 'aucun droit';
	
	private static $standard_role;
	
	public function __construct(){
		self::$standard_role = array(
					self::AUCUN_DROIT => array(),
					'admin' => array(
									'entite:edition',
									'entite:lecture',
									'journal:lecture',
									'fournisseur:lecture',
									'utilisateur:lecture',
									'utilisateur:edition',
									'rh-actes:edition',
									'rh-actes:lecture',
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
									'annuaire:edition'
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
									'rh-actes:edition',
									'rh-actes:lecture',
									'rh-messages:edition',
									'rh-messages:lecture',
									'entite:lecture',
									'journal:lecture',
									'actes:edition',
									'actes:lecture',
									'mailsec:edition',
									'mailsec:lecture'
								),
								
					'agent centre de gestion' => array(
								'rh-actes:edition',
								'rh-actes:lecture',
								'rh-messages:edition',
								'rh-messages:lecture',
								'entite:lecture',
								'journal:lecture',
								'actes:edition',
									'actes:lecture',
									'mailsec:edition',
									'mailsec:lecture'
							),		
								
					'testeur' => array('test:lecture','test:edition')

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
	
}