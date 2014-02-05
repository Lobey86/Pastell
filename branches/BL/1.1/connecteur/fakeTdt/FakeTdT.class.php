<?php
require_once(__DIR__."/../../connecteur-type/TdtAdapter.class.php");


class FakeTdT extends TdtAdapter {
	
	public function postActes(DonneesFormulaire $donneesFormulaire){
		$donneesFormulaire->setData('tedetis_transaction_id',mt_rand(1,mt_getrandmax()));
		return true;
	}
	
	public function getStatus($id_transaction){
		return TdtConnecteur::STATUS_ACQUITTEMENT_RECU;
	}
	
	public function getARActes(){
		return false;
	}
	
	public function getDateAR($id_transaction){
		return date("Y-m-d");
	}
	
	public function getBordereau($id_transaction){
		return false;
	}
	
	public function getListReponsePrefecture($transaction_id){
		return array();
	}
	
}