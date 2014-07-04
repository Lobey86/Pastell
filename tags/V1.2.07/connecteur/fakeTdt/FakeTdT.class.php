<?php

class FakeTdT extends TdtAdapter {
	
	public function getLogicielName(){
		return "FakeTdT";
	}
	
	public function postActes(DonneesFormulaire $donneesFormulaire){
		$donneesFormulaire->setData('tedetis_transaction_id',mt_rand(1,mt_getrandmax()));
		return true;
	}
	
	public function getStatus($id_transaction){
		return TdtConnecteur::STATUS_ACQUITTEMENT_RECU;
	}
	
	public function getARActes(){
		return file_get_contents(__DIR__."/fixtures/ar-actes.xml");
	}
	
	public function getDateAR($id_transaction){
		return date("Y-m-d");
	}
	
	public function getBordereau($id_transaction){
		return file_get_contents(__DIR__."/fixtures/vide.pdf");
	}
	
	public function getActeTamponne($id_transaction){
		return file_get_contents(__DIR__."/fixtures/vide.pdf");
	}
	
	public function getListReponsePrefecture($transaction_id){
		return array();
	}
	
	public function postHelios(DonneesFormulaire $donneesFormulaire){
		$donneesFormulaire->setData('tedetis_transaction_id',mt_rand(1,mt_getrandmax()));
		return true;
	}
	
	public function getStatusHelios($id_transaction){
		return TdtConnecteur::STATUS_HELIOS_INFO;
	}
	
	public function getStatusInfo($status){
		return $status;
	}
	
	public function getFichierRetour($tedetis_transaction_id){
		return '<test></test>';
	}
	
	
}