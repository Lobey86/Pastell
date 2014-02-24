<?php

require_once(PASTELL_PATH . "/pastell-core/ActionExecutor.class.php");
require_once(__DIR__ . "/BLDisplayValue.class.php");
require_once(__DIR__ . "/BLDisplayHtml.class.php");
require_once(__DIR__ . "/BLDisplayText.class.php");
require_once(__DIR__ . "/BLDisplayJson.class.php");

abstract class BLActionExecutor extends ActionExecutor {

    // Attributs génériques 

    const ATTR_ERREUR_DETAIL = 'erreur_detail';
    // Clés pour retour goFonctionnel
    const GO_KEY_ETAT = 'goEtat';
    const GO_KEY_MESSAGE = 'goMessage';
    const GO_KEY_JOURNALINFOS = 'goInfos';
    // Attributs pour le message du journal
    const KEY_JOURNAL_PILOTE = 'app';
    const KEY_JOURNAL_MESSAGE = 'msg';

    protected function isActionAuto() {
        return $this->id_u == 0;
    }

    protected function exceptionToJson(Exception $ex) {
        $json = array(
            'date' => date('d/m/Y H:i:s'),
            'code' => $ex->getCode(),
            'file' => $ex->getFile(),
            'line' => $ex->getLine(),
            'message' => utf8_encode($ex->getMessage()),
            'trace' => utf8_encode_array($ex->getTrace()));
        $json = json_encode($json);
        return $json;
    }

}
