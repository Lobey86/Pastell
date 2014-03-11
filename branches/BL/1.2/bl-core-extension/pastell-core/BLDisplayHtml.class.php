<?php

require_once(__DIR__ . "/BLDisplayText.class.php");

class BLDisplayHtml extends BLDisplayText {

    protected function encode($text) {
        $result = htmlentities($text);
        return $result;
    }

    protected function formatCellHeader($colIndex, $colName) {
        $result = '<th>' . $colName . '</th>';
        return $result;
    }

    protected function formatRowHeader($rowValue) {
        $result = '<tr align="left">' . $rowValue . '</tr>';
        return $result;
    }

    protected function formatCell($rowIndex, $colIndex, $colName, $cellValue) {
        $result = '<td>' . $cellValue . '</td>';
        return $result;
    }

    protected function formatRow($rowIndex, $rowValue) {
        $result = '<tr>' . $rowValue . '</tr>';
        return $result;
    }

    protected function formatTable($tableValue) {
        $border = $this->hasHeader ? '1' : '0';
        $result = '<table border=' . $border . ' cellspacing=0>'
                . $tableValue
                . '</table>';
        return $result;
    }

    protected function concat(&$target, &$source) {
        if ($target) {
            $target .= '<br>';
        }
        $target .= $source;
        return $target;
    }

    public function hyperlinkDisplay($value, $url, $doEncoding = true) {
        $valueDisplay = $this->valueDisplay($value, $doEncoding);
        $result = '<a href="' . $url . '" target="_blank">' . $valueDisplay . '</a>';
        return $result;
    }

}
