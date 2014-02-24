<?php

require_once(__DIR__ . "/BLDisplayValue.class.php");

class BLDisplayJson extends BLDisplayValue {

    protected function encode($text) {
        // L'encodage est réalisé en amont
        return $text;
    }

    protected function formatCellHeader($colIndex, $colName) {
        return $colName;
    }

    protected function formatRowHeader($rowValue) {
        return $rowValue;
    }

    protected function formatCell($rowIndex, $colIndex, $colName, $cellValue) {
        return $cellvalue;
    }

    protected function formatRow($rowIndex, $rowValue) {
        return $rowValue;
    }

    protected function formatTable($tableValue) {
        return $tableValue;
    }

    protected function concat(&$target, &$source) {
        $target[] = $source;
        return $target;
    }

    protected function arrayDisplay(array $array, $doEncoding = true) {
        return $array;
    }

}
