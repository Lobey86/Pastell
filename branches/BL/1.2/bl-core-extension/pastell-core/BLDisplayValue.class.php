<?php

abstract class BLDisplayValue {

    protected $hasHeader;
    private $message;

    abstract protected function encode($text);

    abstract protected function formatCellHeader($colIndex, $colName);

    abstract protected function formatRowHeader($rowValue);

    abstract protected function formatCell($rowIndex, $colIndex, $colName, $cellValue);

    abstract protected function formatRow($rowIndex, $rowValue);

    abstract protected function formatTable($tableValue);

    abstract protected function concat(&$value1, &$value2);

    abstract protected function arrayDisplay(array $array, $doEncoding = true);

    public function stringDisplay($string, $doEncoding = true) {
        if ($doEncoding) {
            $valueDisplay = $this->encode($string);
        } else {
            $valueDisplay = &$string;
        }
        return $valueDisplay;
    }

    public function valueDisplay($value, $doEncoding = true) {
        if (is_string($value)) {
            $valueDisplay = $this->stringDisplay($value, $doEncoding);
        } else {
            $valueDisplay = $this->arrayDisplay($value, $doEncoding);
        }
        return $valueDisplay;
    }

    public function hyperlinkDisplay($value, $url, $doEncoding = true) {
        // Par défaut, la valeur, sans hyperlink
        return $this->valueDisplay($value, $doEncoding);
    }

    public function add($value, $doEncoding = true) {
        $valueDisplay = $this->valueDisplay($value, $doEncoding);
        $this->concat($this->message, $valueDisplay);
        return $this;
    }

    public function getMessage() {
        return $this->message;
    }

    public static function getInstance($php_cli, $from_api) {
        if ($php_cli) {
            $instance = new BLDisplayText();
        } elseif ($from_api) {
            $instance = new BLDisplayJson();
        } else {
            $instance = new BLDisplayHtml();
        }
        return $instance;
    }

    public function hyperlinkDisplayDocument($value, $id_d, $id_e, $doEncoding = true) {
        $url = SITE_BASE . 'document/detail.php?id_d=' . $id_d . '&id_e=' . $id_e;
        $valueDisplay = $this->hyperlinkDisplay($value, $url, $doEncoding);
        return $valueDisplay;
    }

    public function hyperlinkDisplayEntite($value, $id_e, $doEncoding = true) {
        $url = SITE_BASE . 'document/index.php?id_e=' . $id_e;
        $valueDisplay = $this->hyperlinkDisplay($value, $url, $doEncoding);
        return $valueDisplay;
    }

    private function hyperlinkDisplayEntiteDetail($value, $id_e, $page = 0, $doEncoding = true) {
        $url = SITE_BASE . 'entite/detail.php?id_e=' . $id_e . '&page=' . $page;
        $valueDisplay = $this->hyperlinkDisplay($value, $url, $doEncoding);
        return $valueDisplay;
    }

    public function hyperlinkDisplayEntiteDetailConnecteurs($value, $id_e, $doEncoding = true) {
        return $this->hyperlinkDisplayEntiteDetail($value, $id_e, 3, $doEncoding);
    }

    public function hyperlinkDisplayEntiteConnecteur($value, $id_ce, $doEncoding = true) {
        $url = SITE_BASE . 'connecteur/edition.php?id_ce=' . $id_ce;
        $valueDisplay = $this->hyperlinkDisplay($value, $url, $doEncoding);
        return $valueDisplay;
    }

}
