<?php
class TdtTeletransmettre extends ActionExecutor {
	
	public function go(){
		
		$tdt = $this->getConnecteur("TdT");
		$redirect_url = $tdt->getRedirectURLForTeletransimission();
		$tedetis_transaction_id = $this->getDonneesFormulaire()->get('tedetis_transaction_id');

		$this->changeAction("teletransmission-tdt", "La télétransmission a été ordonné depuis Pastell");
		
		$url_retour = SITE_BASE."/document/action.php?id_d={$this->id_d}&id_e={$this->id_e}&action=return-teletransmission-tdt&error=%%ERROR%%&message=%%MESSAGE%%";
		
		$to = $redirect_url."?id={$tedetis_transaction_id}&url_return=".urlencode($url_retour);
		header("Location: $to");
		exit;		
	}
}