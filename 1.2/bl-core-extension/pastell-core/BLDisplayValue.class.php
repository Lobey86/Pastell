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

    public function add($value, $doEncoding = true) {
        $BLDisplayValue = $this->valueDisplay($value, $doEncoding);
        $this->concat($this->message, $BLDisplayValue);
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

}
